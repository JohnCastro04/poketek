@props(['inputId' => 'password', 'position' => 'bottom'])

<div class="password-strength-container" data-input-id="{{ $inputId }}">
    <div class="password-requirements-tooltip {{ $position }}" id="requirements-{{ $inputId }}">
        <div class="tooltip-arrow"></div>
        <div class="tooltip-content">
            <h6 class="requirements-title">Requisitos de la contraseña:</h6>
            <div class="requirements-list">
                <div class="requirement" data-rule="length">
                    <span class="indicator"></span>
                    <span class="text">Al menos 8 caracteres</span>
                </div>
                <div class="requirement" data-rule="uppercase">
                    <span class="indicator"></span>
                    <span class="text">Una letra mayúscula (A-Z)</span>
                </div>
                <div class="requirement" data-rule="lowercase">
                    <span class="indicator"></span>
                    <span class="text">Una letra minúscula (a-z)</span>
                </div>
                <div class="requirement" data-rule="number">
                    <span class="indicator"></span>
                    <span class="text">Un número (0-9)</span>
                </div>
                <div class="requirement" data-rule="symbol">
                    <span class="indicator"></span>
                    <span class="text">Un símbolo especial (@$!%*?&)</span>
                </div>
            </div>
            <div class="strength-meter">
                <div class="strength-label">Fortaleza:</div>
                <div class="strength-bar">
                    <div class="strength-fill" data-strength="0"></div>
                </div>
                <div class="strength-text">Muy débil</div>
            </div>
        </div>
    </div>
</div>


<style>
:root {
    --golden-bloom: #F2C572;
    --golden-bloom-light: #FFD89C;
    --midnight-abyss: #121621;
    --midnight-abyss-light: #1A2030;
}

.password-strength-container {
    position: relative;
    display: inline-block;
    width: 100%;
}

.password-requirements-tooltip {
    position: absolute;
    background: white;
    border: 2px solid var(--golden-bloom-light);
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(18, 22, 33, 0.15);
    padding: 20px;
    width: 300px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-15px) scale(0.95);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 0.875rem;
    backdrop-filter: blur(10px);
}

.password-requirements-tooltip.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.password-requirements-tooltip.bottom {
    top: 100%;
    left: 0;
    margin-top: 12px;
}

.password-requirements-tooltip.top {
    bottom: 100%;
    left: 0;
    margin-bottom: 12px;
    transform-origin: bottom center;
}

.password-requirements-tooltip.right {
    left: 100%;
    top: 0;
    margin-left: 12px;
    transform-origin: left center;
}

.tooltip-arrow {
    position: absolute;
    width: 0;
    height: 0;
}

.password-requirements-tooltip.bottom .tooltip-arrow {
    top: -10px;
    left: 24px;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-bottom: 10px solid var(--golden-bloom-light);
}

.password-requirements-tooltip.top .tooltip-arrow {
    bottom: -10px;
    left: 24px;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 10px solid var(--golden-bloom-light);
}

.password-requirements-tooltip.right .tooltip-arrow {
    left: -10px;
    top: 24px;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-right: 10px solid var(--golden-bloom-light);
}

.requirements-title {
    margin: 0 0 16px 0;
    font-weight: 700;
    color: var(--midnight-abyss);
    font-size: 1rem;
    text-align: center;
    letter-spacing: 0.5px;
}

.requirements-list {
    margin-bottom: 20px;
}

.requirement {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    padding: 6px 0;
    transition: all 0.3s ease;
    border-radius: 6px;
    padding-left: 4px;
}

.requirement:hover {
    background-color: rgba(242, 197, 114, 0.1);
    padding-left: 8px;
}

.indicator {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    margin-right: 12px;
    background-color: #9ca3af;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
    border: 2px solid transparent;
    position: relative;
}

.requirement.valid .indicator {
    background-color: var(--golden-bloom);
    border-color: var(--golden-bloom-light);
    transform: scale(1.2);
    box-shadow: 0 0 0 3px rgba(242, 197, 114, 0.3);
}

.requirement.valid .indicator::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 8px;
    font-weight: bold;
}

.requirement.invalid .indicator {
    background-color: #ef4444;
    border-color: #fca5a5;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-2px); }
    75% { transform: translateX(2px); }
}

.requirement.valid .text {
    color: var(--midnight-abyss);
    font-weight: 600;
    text-decoration: line-through;
    opacity: 0.8;
}

.requirement.invalid .text {
    color: #ef4444;
    font-weight: 500;
}

.requirement .text {
    color: var(--midnight-abyss-light);
    transition: all 0.3s ease;
    font-size: 0.85rem;
    line-height: 1.4;
}

.strength-meter {
    border-top: 2px solid var(--golden-bloom-light);
    padding-top: 16px;
}

.strength-label {
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--midnight-abyss);
    font-size: 0.9rem;
    text-align: center;
}

.strength-bar {
    width: 100%;
    height: 8px;
    background-color: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 8px;
    position: relative;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 4px;
    position: relative;
    overflow: hidden;
}

.strength-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.strength-fill[data-strength="0"] { 
    width: 0%; 
    background: linear-gradient(90deg, #9ca3af, #6b7280);
}
.strength-fill[data-strength="1"] { 
    width: 20%; 
    background: linear-gradient(90deg, #ef4444, #dc2626);
}
.strength-fill[data-strength="2"] { 
    width: 40%; 
    background: linear-gradient(90deg, #f97316, #ea580c);
}
.strength-fill[data-strength="3"] { 
    width: 60%; 
    background: linear-gradient(90deg, #eab308, #ca8a04);
}
.strength-fill[data-strength="4"] { 
    width: 80%; 
    background: linear-gradient(90deg, var(--golden-bloom-light), var(--golden-bloom));
}
.strength-fill[data-strength="5"] { 
    width: 100%; 
    background: linear-gradient(90deg, var(--golden-bloom), #d4a574);
}

.strength-text {
    font-size: 0.85rem;
    font-weight: 600;
    text-align: center;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .password-requirements-tooltip {
        width: 280px;
        font-size: 0.8rem;
        padding: 16px;
    }
    
    .password-requirements-tooltip.right {
        left: 0;
        top: 100%;
        margin-left: 0;
        margin-top: 12px;
    }
    
    .password-requirements-tooltip.right .tooltip-arrow {
        left: 24px;
        top: -10px;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid var(--golden-bloom-light);
        border-top: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('[data-input-id="{{ $inputId }}"]');
    const tooltip = container.querySelector('#requirements-{{ $inputId }}');
    const passwordInput = document.getElementById('{{ $inputId }}');
    
    if (!passwordInput || !tooltip) return;

    const requirements = {
        length: tooltip.querySelector('[data-rule="length"]'),
        uppercase: tooltip.querySelector('[data-rule="uppercase"]'),
        lowercase: tooltip.querySelector('[data-rule="lowercase"]'),
        number: tooltip.querySelector('[data-rule="number"]'),
        symbol: tooltip.querySelector('[data-rule="symbol"]')
    };

    const strengthFill = tooltip.querySelector('.strength-fill');
    const strengthText = tooltip.querySelector('.strength-text');

    const strengthLabels = [
    'Sin contraseña',
    'Muy débil',
    'Débil', 
    'Aceptable',
    'Fuerte',
    'Muy fuerte'
];

    let isTooltipVisible = false;

    function validatePassword(password) {
        const validations = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /\d/.test(password),
            symbol: /[@$!%*?&]/.test(password)
        };

        let strength = 0;
        const hasContent = password.length > 0;

        // Actualizar cada requisito
        Object.keys(validations).forEach(key => {
            const requirement = requirements[key];
            const isValid = validations[key];

            // Remover clases existentes
            requirement.classList.remove('valid', 'invalid');

            if (hasContent) {
                if (isValid) {
                    requirement.classList.add('valid');
                    strength++;
                } else {
                    requirement.classList.add('invalid');
                }
            }
        });

        // Actualizar medidor de fortaleza
        strengthFill.setAttribute('data-strength', strength);
        strengthText.textContent = strengthLabels[strength];
        strengthText.style.color = getStrengthColor(strength);
    }

    function getStrengthColor(strength) {
        const colors = [
            '#9ca3af', 
            '#ef4444', 
            '#f97316', 
            '#eab308', 
            'var(--golden-bloom)', 
            'var(--golden-bloom)'
        ];
        return colors[strength] || colors[0];
    }

    function showTooltip() {
        isTooltipVisible = true;
        tooltip.classList.add('show');
    }

    function hideTooltip() {
        isTooltipVisible = false;
        tooltip.classList.remove('show');
    }

    // Event listeners
    passwordInput.addEventListener('focus', showTooltip);
    
    passwordInput.addEventListener('blur', function() {
        // Ocultar inmediatamente cuando se sale del campo
        setTimeout(hideTooltip, 100);
    });
    
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
        if (this.value.length > 0 && !isTooltipVisible) {
            showTooltip();
        }
    });

    // Validación inicial
    validatePassword(passwordInput.value);
});
</script>