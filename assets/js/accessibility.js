(function () {
  'use strict';

  const storageKey = 'mathplay-accessibility';
  const defaultSettings = {
    fontScale: 1,
    highContrast: false,
    underlinedLinks: false
  };
  let settings = { ...defaultSettings };

  try {
    const savedSettings = window.localStorage.getItem(storageKey);
    if (savedSettings) {
      const parsedSettings = JSON.parse(savedSettings);
      const fontScale = Number(parsedSettings.fontScale);
      settings = {
        fontScale: [1, 1.1, 1.2, 1.3].includes(fontScale) ? fontScale : 1,
        highContrast: parsedSettings.highContrast === true,
        underlinedLinks: parsedSettings.underlinedLinks === true
      };
    }
  } catch (error) {
    console.warn('[Accessibility] Não foi possível carregar as preferências salvas.', error);
  }

  function saveSettings() {
    try {
      window.localStorage.setItem(storageKey, JSON.stringify(settings));
    } catch (error) {
      console.warn('[Accessibility] Não foi possível salvar as preferências.', error);
    }
  }

  function applySettings() {
    document.documentElement.style.setProperty('--accessibility-font-scale', settings.fontScale);
    document.body.classList.toggle('accessibility-high-contrast', settings.highContrast);
    document.body.classList.toggle('accessibility-links-underlined', settings.underlinedLinks);
  }

  function init() {
    if (document.querySelector('.accessibility-widget')) {
      return;
    }

    const widget = document.createElement('div');
    widget.className = 'accessibility-widget';
    widget.innerHTML = `
      <section class="accessibility-panel" id="accessibility-panel" role="region" aria-labelledby="accessibility-title" hidden>
        <h2 id="accessibility-title">Acessibilidade</h2>
        <p id="accessibility-font-status" aria-live="polite"></p>
        <div class="accessibility-controls">
          <button type="button" data-accessibility-action="increase-font" aria-label="Aumentar tamanho do texto">A+ Aumentar texto</button>
          <button type="button" data-accessibility-action="decrease-font" aria-label="Diminuir tamanho do texto">A− Diminuir texto</button>
          <button type="button" data-accessibility-action="contrast" aria-pressed="false">Alto contraste: desligado</button>
          <button type="button" data-accessibility-action="underline" aria-pressed="false">Sublinhar links: desligado</button>
          <button type="button" data-accessibility-action="reset">Restaurar padrão</button>
        </div>
      </section>
      <button
        type="button"
        class="accessibility-toggle"
        aria-label="Abrir opções de acessibilidade"
        aria-controls="accessibility-panel"
        aria-expanded="false"
        title="Acessibilidade"
      >
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <circle cx="12" cy="4" r="2"></circle>
          <path d="M4 8h16M12 8v4m0 0-4 8m4-8 4 8"></path>
          <circle cx="12" cy="13" r="9"></circle>
        </svg>
      </button>
    `;
    document.body.appendChild(widget);

    const toggle = widget.querySelector('.accessibility-toggle');
    const panel = widget.querySelector('.accessibility-panel');
    const fontStatus = widget.querySelector('#accessibility-font-status');
    const contrastButton = widget.querySelector('[data-accessibility-action="contrast"]');
    const underlineButton = widget.querySelector('[data-accessibility-action="underline"]');

    function updateControls() {
      fontStatus.textContent = `Tamanho do texto: ${Math.round(settings.fontScale * 100)}%.`;
      contrastButton.setAttribute('aria-pressed', String(settings.highContrast));
      contrastButton.textContent = `Alto contraste: ${settings.highContrast ? 'ligado' : 'desligado'}`;
      underlineButton.setAttribute('aria-pressed', String(settings.underlinedLinks));
      underlineButton.textContent = `Sublinhar links: ${settings.underlinedLinks ? 'ligado' : 'desligado'}`;
      applySettings();
    }

    toggle.addEventListener('click', function () {
      const isOpen = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!isOpen));
      toggle.setAttribute('aria-label', isOpen ? 'Abrir opções de acessibilidade' : 'Fechar opções de acessibilidade');
      panel.hidden = isOpen;
      if (!isOpen) {
        panel.querySelector('button').focus();
      }
    });

    widget.addEventListener('click', function (event) {
      const button = event.target.closest('[data-accessibility-action]');
      if (!button) {
        return;
      }

      switch (button.dataset.accessibilityAction) {
        case 'increase-font':
          settings.fontScale = Math.min(1.3, Math.round((settings.fontScale + 0.1) * 10) / 10);
          break;
        case 'decrease-font':
          settings.fontScale = Math.max(1, Math.round((settings.fontScale - 0.1) * 10) / 10);
          break;
        case 'contrast':
          settings.highContrast = !settings.highContrast;
          break;
        case 'underline':
          settings.underlinedLinks = !settings.underlinedLinks;
          break;
        case 'reset':
          settings = { ...defaultSettings };
          break;
        default:
          return;
      }

      updateControls();
      saveSettings();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !panel.hidden) {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir opções de acessibilidade');
        toggle.focus();
      }
    });

    document.addEventListener('click', function (event) {
      if (!panel.hidden && !widget.contains(event.target)) {
        panel.hidden = true;
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Abrir opções de acessibilidade');
      }
    });

    updateControls();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
}());
