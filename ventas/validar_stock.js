/**
 * Validador de Stock en Tiempo Real
 * Previene que el stock quede en negativo y muestra alertas sin recargar la página
 */

class ValidadorStock {
    constructor() {
        this.cache = new Map(); // Cache para evitar consultas repetitivas
        this.timeoutId = null;
        this.verificandoStock = false;
    }

    // Verificar stock individual con debounce
    async verificarStockIndividual(productoId, cantidad, callback = null) {
        if (this.verificandoStock) return;
        
        // Limpiar timeout anterior
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }

        // Debounce para evitar múltiples llamadas
        this.timeoutId = setTimeout(async () => {
            try {
                this.verificandoStock = true;
                
                const cacheKey = `${productoId}_${cantidad}`;
                
                // Verificar cache primero
                if (this.cache.has(cacheKey)) {
                    const cachedResult = this.cache.get(cacheKey);
                    if (callback) callback(cachedResult);
                    return cachedResult;
                }

                const response = await fetch('verificar_stock.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        accion: 'verificar_individual',
                        producto_id: productoId,
                        cantidad: cantidad
                    })
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    // Guardar en cache por 30 segundos
                    this.cache.set(cacheKey, data.data);
                    setTimeout(() => this.cache.delete(cacheKey), 30000);
                    
                    if (callback) callback(data.data);
                    return data.data;
                } else {
                    throw new Error(data.error || 'Error desconocido');
                }

            } catch (error) {
                console.error('Error verificando stock:', error);
                const errorResult = {
                    existe: false,
                    error: true,
                    mensaje: 'Error de conexión al verificar stock'
                };
                if (callback) callback(errorResult);
                return errorResult;
            } finally {
                this.verificandoStock = false;
            }
        }, 500); // Debounce de 500ms
    }

    // Verificar múltiples productos
    async verificarStockMultiple(productos) {
        try {
            const response = await fetch('verificar_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    accion: 'verificar_multiple',
                    productos: productos
                })
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            return data.success ? data.data : null;

        } catch (error) {
            console.error('Error verificando stock múltiple:', error);
            return null;
        }
    }

    // Obtener stock actual sin verificar cantidad
    async obtenerStockActual(productoId) {
        try {
            const response = await fetch(`verificar_stock.php?producto_id=${productoId}`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            return data.success ? data : null;

        } catch (error) {
            console.error('Error obteniendo stock:', error);
            return null;
        }
    }

    // Mostrar alerta de stock insuficiente
    mostrarAlertaStock(nombre, stockActual, cantidadSolicitada) {
        Swal.fire({
            icon: 'error',
            title: '¡Stock Insuficiente!',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p><strong>Producto:</strong> ${nombre}</p>
                    <p><strong>Stock disponible:</strong> ${stockActual} unidades</p>
                    <p><strong>Cantidad solicitada:</strong> ${cantidadSolicitada} unidades</p>
                    <p style="color: #e74c3c; font-weight: bold;">
                        Faltan ${cantidadSolicitada - stockActual} unidades
                    </p>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#8e44ad'
        });
    }

    // Mostrar alerta de múltiples errores
    mostrarAlertaMultiple(errores) {
        const listaErrores = errores.map(error => `<li>${error}</li>`).join('');
        
        Swal.fire({
            icon: 'error',
            title: '¡Problemas de Stock!',
            html: `
                <div style="text-align: left; padding: 10px;">
                    <p>Se encontraron los siguientes problemas:</p>
                    <ul style="padding-left: 20px;">
                        ${listaErrores}
                    </ul>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#8e44ad'
        });
    }

    // Validar todo el formulario antes de enviar
    async validarFormulario(formData) {
        const productos = [];
        
        // Extraer productos del FormData
        const productosIds = formData.getAll('producto_id[]');
        const cantidades = formData.getAll('cantidad[]');
        
        for (let i = 0; i < productosIds.length; i++) {
            if (productosIds[i] && cantidades[i]) {
                productos.push({
                    id: parseInt(productosIds[i]),
                    cantidad: parseInt(cantidades[i])
                });
            }
        }

        if (productos.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin productos',
                text: 'Debe agregar al menos un producto a la venta.',
                confirmButtonColor: '#8e44ad'
            });
            return false;
        }

        const verificacion = await this.verificarStockMultiple(productos);
        
        if (!verificacion) {
            Swal.fire({
                icon: 'error',
                title: 'Error de verificación',
                text: 'No se pudo verificar el stock. Intente nuevamente.',
                confirmButtonColor: '#8e44ad'
            });
            return false;
        }

        if (!verificacion.stock_suficiente) {
            this.mostrarAlertaMultiple(verificacion.errores);
            return false;
        }

        return true;
    }

    // Limpiar cache
    limpiarCache() {
        this.cache.clear();
    }

    // Actualizar stock en el DOM después de una venta exitosa
    actualizarStockEnSelect(productoId, nuevoStock) {
        const selects = document.querySelectorAll('.producto-select');
        selects.forEach(select => {
            const option = select.querySelector(`option[value="${productoId}"]`);
            if (option) {
                const texto = option.textContent;
                const nuevoTexto = texto.replace(/Stock: \d+/, `Stock: ${nuevoStock}`);
                option.textContent = nuevoTexto;
                
                // Si el stock es 0, deshabilitar la opción
                if (nuevoStock <= 0) {
                    option.disabled = true;
                    option.textContent += ' (Sin stock)';
                }
            }
        });
    }
}

// Inicializar validador global
const validadorStock = new ValidadorStock();

// Función para integrar con el código existente
function verificarStockEnTiempoReal(element) {
    const productoItem = element.closest('.producto-item');
    if (!productoItem) return;

    const select = productoItem.querySelector('.producto-select');
    const cantidadInput = productoItem.querySelector('.cantidad');
    const subtotalInput = productoItem.querySelector('.subtotal');

    const productoId = parseInt(select.value);
    const cantidad = parseInt(cantidadInput.value) || 0;

    if (productoId && cantidad > 0) {
        validadorStock.verificarStockIndividual(productoId, cantidad, (resultado) => {
            if (resultado.existe && !resultado.stock_suficiente) {
                // Mostrar alerta
                validadorStock.mostrarAlertaStock(
                    resultado.nombre, 
                    resultado.stock_actual, 
                    resultado.cantidad_solicitada
                );
                
                // Ajustar cantidad al stock disponible
                cantidadInput.value = resultado.stock_actual;
                
                // Agregar indicador visual
                cantidadInput.style.borderColor = '#e74c3c';
                cantidadInput.style.boxShadow = '0 0 0 2px rgba(231, 76, 60, 0.2)';
                
                setTimeout(() => {
                    cantidadInput.style.borderColor = '';
                    cantidadInput.style.boxShadow = '';
                }, 3000);
                
                // Recalcular precios
                if (typeof actualizarPrecios === 'function') {
                    actualizarPrecios();
                }
            } else if (resultado.error) {
                console.error('Error verificando stock:', resultado.mensaje);
            }
        });
    }
}

// Función para validar antes de enviar el formulario
async function validarAntesDeEnviar(form) {
    const formData = new FormData(form);
    const esValido = await validadorStock.validarFormulario(formData);
    
    if (!esValido) {
        return false;
    }
    
    // Mostrar confirmación antes de procesar
    const result = await Swal.fire({
        title: '¿Confirmar venta?',
        text: 'Una vez confirmada, se actualizará el stock de los productos.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, completar venta',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#8e44ad',
        cancelButtonColor: '#95a5a6'
    });
    
    return result.isConfirmed;
}

// Exportar para uso global
window.validadorStock = validadorStock;
window.verificarStockEnTiempoReal = verificarStockEnTiempoReal;
window.validarAntesDeEnviar = validarAntesDeEnviar;