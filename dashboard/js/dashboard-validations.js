/**
 * Archivo de validaciones para Dashboard de Ventas
 * Autor: Sistema de Gestión
 * Fecha: 2025
 */

class DashboardValidator {
    constructor() {
        this.initializeValidations();
    }

    /**
     * Inicializa todas las validaciones del dashboard
     */
    initializeValidations() {
        this.validateDateRange();
        this.validateFormSubmission();
        this.addRealTimeValidation();
    }

    /**
     * Valida que la fecha de fin no sea anterior a la fecha de inicio
     */
    validateDateRange() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
        const fechaFin = document.querySelector('input[name="fecha_fin"]');
        
        if (!fechaInicio || !fechaFin) return;

        const validateDates = () => {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);

            // Limpiar mensajes de error previos
            this.clearErrorMessages();

            if (fechaInicio.value && fechaFin.value) {
                if (fin < inicio) {
                    this.showError('La fecha de fin no puede ser anterior a la fecha de inicio', fechaFin);
                    return false;
                }

                // Validar que no sean fechas futuras
                const hoy = new Date();
                hoy.setHours(23, 59, 59, 999); // Fin del día actual

                if (fin > hoy) {
                    this.showWarning('La fecha de fin es posterior al día de hoy. Los datos pueden estar incompletos.', fechaFin);
                }

                if (inicio > hoy) {
                    this.showError('La fecha de inicio no puede ser futura', fechaInicio);
                    return false;
                }

                // Validar rango máximo (no más de 2 años)
                const dosAniosAtras = new Date();
                dosAniosAtras.setFullYear(dosAniosAtras.getFullYear() - 2);

                if (inicio < dosAniosAtras) {
                    this.showWarning('El rango de fechas es muy amplio. Esto puede afectar el rendimiento.', fechaInicio);
                }
            }

            return true;
        };

        // Agregar eventos de validación
        fechaInicio.addEventListener('change', validateDates);
        fechaFin.addEventListener('change', validateDates);
        fechaFin.addEventListener('blur', validateDates);
    }

    /**
     * Valida el formulario antes del envío
     */
    validateFormSubmission() {
        const forms = document.querySelectorAll('form');
        form.addEventListener('submit', (e) => {
        const isValid = this.validateBeforeSubmit();

        if (!isValid) {
            e.preventDefault();
            this.showError('Por favor, corrija los errores antes de continuar');
            
            // Si hay un botón en loading, restaurarlo
            const loadingBtn = document.querySelector('button[disabled][data-original-text]');
            if (loadingBtn) {
                this.hideLoading(loadingBtn);
            }

            return false; // Cortar flujo de envío
        }
    });

        

    }

    /**
     * Validaciones en tiempo real
     */
    addRealTimeValidation() {
        // Validar botones de período
        const periodoButtons = document.querySelectorAll('button[name="periodo"]');
        
        periodoButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                // Mostrar loading
                this.showLoading(button);
                
                // Validar que hay conexión (simulado)
                if (!navigator.onLine) {
                    e.preventDefault();
                    this.showError('No hay conexión a internet. Verifique su conexión e intente nuevamente.');
                    this.hideLoading(button);
                }
            });
        });
    }

    /**
     * Validaciones antes del envío del formulario
     */
    validateBeforeSubmit() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]');
        const fechaFin = document.querySelector('input[name="fecha_fin"]');
        
        let isValid = true;

        // Validar campos requeridos
        if (!fechaInicio.value || !fechaFin.value) {
            this.showError('Las fechas de inicio y fin son obligatorias');
            isValid = false;
        }

        // Validar formato de fecha
        if (!this.isValidDateFormat(fechaInicio.value) || !this.isValidDateFormat(fechaFin.value)) {
            this.showError('El formato de fecha no es válido');
            isValid = false;
        }

        // Validar rango de fechas
        if (fechaInicio.value && fechaFin.value) {
            const inicio = new Date(fechaInicio.value);
            const fin = new Date(fechaFin.value);
            
            if (fin < inicio) {
                this.showError('La fecha de fin no puede ser anterior a la fecha de inicio');
                isValid = false;
            }
        }

        return isValid;
    }

    /**
     * Valida el formato de fecha YYYY-MM-DD
     */
    isValidDateFormat(dateString) {
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(dateString)) return false;
        
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }

    /**
     * Muestra un mensaje de error
     */
    showError(message, element = null) {
        this.showMessage(message, 'error', element);
    }

    /**
     * Muestra un mensaje de advertencia
     */
    showWarning(message, element = null) {
        this.showMessage(message, 'warning', element);
    }

    /**
     * Muestra un mensaje de éxito
     */
    showSuccess(message, element = null) {
        this.showMessage(message, 'success', element);
    }

    /**
     * Función general para mostrar mensajes
     */
    showMessage(message, type = 'info', element = null) {
        // Remover alertas existentes
        this.clearErrorMessages();

        // Crear alerta
        const alert = document.createElement('div');
        alert.className = `alert alert-${this.getBootstrapAlertClass(type)} alert-dismissible fade show validation-alert`;
        alert.innerHTML = `
            <i class="bi bi-${this.getAlertIcon(type)}"></i>
            <strong>${this.getAlertTitle(type)}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Agregar estilos si el elemento existe
        if (element) {
            element.classList.add(`is-${type === 'error' ? 'invalid' : 'valid'}`);
        }

        // Insertar alerta en el DOM
        const container = document.querySelector('.container-fluid');
        if (container) {
            container.insertBefore(alert, container.firstChild);
        }

        // Auto-cerrar después de 5 segundos (excepto errores)
        if (type !== 'error') {
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
    }

    /**
     * Obtiene la clase de Bootstrap según el tipo de mensaje
     */
    getBootstrapAlertClass(type) {
        const classes = {
            'error': 'danger',
            'warning': 'warning',
            'success': 'success',
            'info': 'info'
        };
        return classes[type] || 'info';
    }

    /**
     * Obtiene el icono según el tipo de mensaje
     */
    getAlertIcon(type) {
        const icons = {
            'error': 'exclamation-triangle-fill',
            'warning': 'exclamation-triangle',
            'success': 'check-circle-fill',
            'info': 'info-circle-fill'
        };
        return icons[type] || 'info-circle';
    }

    /**
     * Obtiene el título según el tipo de mensaje
     */
    getAlertTitle(type) {
        const titles = {
            'error': 'Error',
            'warning': 'Advertencia',
            'success': 'Éxito',
            'info': 'Información'
        };
        return titles[type] || 'Información';
    }

    /**
     * Limpia todos los mensajes de error
     */
    clearErrorMessages() {
        const alerts = document.querySelectorAll('.validation-alert');
        alerts.forEach(alert => alert.remove());

        // Limpiar clases de validación
        const inputs = document.querySelectorAll('.is-invalid, .is-valid');
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
        });
    }

    /**
     * Muestra indicador de carga
     */
    showLoading(button) {
        const originalText = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cargando...';
        button.disabled = true;
        button.dataset.originalText = originalText;
    }

    /**
     * Oculta indicador de carga
     */
    hideLoading(button) {
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            button.disabled = false;
            delete button.dataset.originalText;
        }
    }

    /**
     * Validaciones específicas del negocio
     */
    validateBusinessRules() {
        // Validar que las ventas no sean negativas
        const ventasElements = document.querySelectorAll('[data-ventas]');
        ventasElements.forEach(element => {
            const ventas = parseFloat(element.dataset.ventas || 0);
            if (ventas < 0) {
                this.showWarning('Se detectaron valores de ventas negativos. Verifique los datos.');
            }
        });

        // Validar proporciones de gastos
        const ventasTotal = parseFloat(document.querySelector('[data-ventas-total]')?.dataset.ventasTotal || 0);
        const gastosTotal = parseFloat(document.querySelector('[data-gastos-total]')?.dataset.gastosTotal || 0);
        
        if (ventasTotal > 0 && gastosTotal > 0) {
            const proporcionGastos = (gastosTotal / ventasTotal) * 100;
            
            if (proporcionGastos > 80) {
                this.showWarning('Los gastos representan más del 80% de las ventas. Revise la estructura de costos.');
            } else if (proporcionGastos > 60) {
                this.showWarning('Los gastos representan más del 60% de las ventas. Considere optimizar los costos.');
            }
        }
    }

    /**
     * Inicializa tooltips informativos
     */
    initializeTooltips() {
        // Agregar tooltips a elementos clave
        const tooltips = [
            {
                selector: '[data-ventas-total]',
                text: 'Incluye el total de productos vendidos más los envíos'
            },
            {
                selector: '[data-beneficio]',
                text: 'Calculado como: Ventas - (Gastos + Compras)'
            },
            {
                selector: '[data-gastos-fijos]',
                text: 'Gastos que no varían con el volumen de ventas'
            },
            {
                selector: '[data-gastos-variables]',
                text: 'Gastos que fluctúan según la actividad del negocio'
            }
        ];

        tooltips.forEach(tooltip => {
            const elements = document.querySelectorAll(tooltip.selector);
            elements.forEach(element => {
                element.setAttribute('data-bs-toggle', 'tooltip');
                element.setAttribute('data-bs-placement', 'top');
                element.setAttribute('title', tooltip.text);
            });
        });

        // Inicializar tooltips de Bootstrap
        if (typeof bootstrap !== 'undefined') {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }
}

// Estilos CSS adicionales para las validaciones
const validationStyles = `
<style>
.validation-alert {
    margin-bottom: 1rem;
    border-radius: 0.375rem;
}

.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.is-valid {
    border-color: #198754 !important;
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
}

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

@media (max-width: 768px) {
    .validation-alert {
        font-size: 0.875rem;
    }
}
</style>
`;

// Inyectar estilos al DOM
if (document.head) {
    document.head.insertAdjacentHTML('beforeend', validationStyles);
}

// Inicializar validaciones cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    const validator = new DashboardValidator();
    
    // Ejecutar validaciones de reglas de negocio después de cargar
    setTimeout(() => {
        validator.validateBusinessRules();
        validator.initializeTooltips();
    }, 1000);
    
    // Hacer el validador accesible globalmente
    window.dashboardValidator = validator;
});

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DashboardValidator;
}