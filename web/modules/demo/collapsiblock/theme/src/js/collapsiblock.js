// eslint-disable-next-line
import { up, down } from 'slide-element';
import '../css/collapsiblock.css';
import '../css/collapsiblock-rtl.css';

((Drupal, cookies, once) => {
  Drupal.Collapsiblock = Drupal.Collapsiblock || {};

  Drupal.behaviors.collapsiblock = {
    attach: (context, settings) => {
      const cookieString = cookies.get('collapsiblock');
      const cookieData = cookieString ? JSON.parse(cookieString) : {};
      const allowCollapseActivePages = settings.collapsiblock.active_pages;
      const slideSpeed = parseInt(settings.collapsiblock.slide_speed, 10);
      const cookieLifetime = settings.collapsiblock.cookie_lifetime;

      const titleElements = once(
        'collapsiblock',
        document.querySelectorAll('.collapsiblockTitle'),
      );

      titleElements.forEach((titleElement) => {
        // Only add the button if one or more of the children are visible.
        if (
          titleElement.children.length === 0 ||
          titleElement.children.length ===
            titleElement.querySelectorAll('.visually-hidden').length
        ) {
          return;
        }

        // Status values: 1 = not collapsible, 2 = collapsible and expanded,
        // 3 = collapsible and collapsed, 4 = always collapsed,
        // 5 = always expanded
        const status = parseInt(titleElement.dataset.collapsiblockAction, 10);
        if (status === 1) {
          return;
        }

        const useCookie = status === 2 || status === 3;

        let targetElement = titleElement.nextElementSibling;
        while (
          targetElement &&
          targetElement.hasAttribute('data-contextual-id')
        ) {
          targetElement = targetElement.nextElementSibling;
        }

        // If there's no block body, escape this iteration.
        if (!targetElement) {
          return;
        }

        const id = titleElement.id.split('-').pop();
        titleElement.innerHTML = `<button id="#collapse-${id}" aria-controls="collapse-${id}-content">${titleElement.innerHTML}</button>`;
        targetElement.classList.add(['collapsiblockContent']);
        targetElement.setAttribute('id', `collapse-${id}-content`);

        const collapseButton = document.getElementById(`#collapse-${id}`);

        /**
         * Click event
         */
        titleElement.addEventListener('click', () => {
          if (titleElement.classList.contains('collapsiblockTitleCollapsed')) {
            titleElement.classList.remove('collapsiblockTitleCollapsed');
            targetElement.classList.remove('collapsiblockContentCollapsed');
            down(targetElement, { duration: slideSpeed });
            collapseButton.setAttribute('aria-expanded', true);

            if (useCookie) {
              cookieData[id] = 1;
            }
          } else {
            titleElement.classList.add('collapsiblockTitleCollapsed');
            targetElement.classList.add('collapsiblockContentCollapsed');
            up(targetElement, { duration: slideSpeed });
            collapseButton.setAttribute('aria-expanded', false);

            if (useCookie) {
              cookieData[id] = 0;
            }
          }

          // Mount cookie options.
          const cookieOptions = {
            path: drupalSettings.path.baseUrl,
          };

          // Add cookie expiration time if it's set on config.
          if (cookieLifetime) {
            cookieOptions.expires = parseFloat(cookieLifetime);
          }

          // Stringify the object in JSON format for saving in the cookie.
          cookies.set(
            'collapsiblock',
            JSON.stringify(cookieData),
            cookieOptions,
          );
        });

        /**
         * Initial state.
         *
         * Collapse if any of the following criteria are met:
         * - Blocks are always collapsed.
         * - Blocks are collapsed by default and there's no cookie data.
         * - Block is allowed to use cookie data and cookie says it should
         *   be collapsed.
         */
        if (
          status === 4 ||
          (status === 3 && cookieData[id] === undefined) ||
          (useCookie && cookieData[id] === 0)
        ) {
          const numberActiveMenuItems =
            targetElement.querySelectorAll('a.is-active').length;
          if (
            (numberActiveMenuItems > 0 && !allowCollapseActivePages) ||
            // Allow block content to assign class
            // 'collapsiblock-force-open' to it's content to force itself
            // to stay open. E.g. useful if block contains a form that was
            // just updated with ajax and should be visible
            targetElement.classList.contains('collapsiblock-force-open') ||
            targetElement.querySelectorAll('.collapsiblock-force-open').length >
              0
          ) {
            collapseButton.setAttribute('aria-expanded', true);
            return;
          }

          titleElement.classList.add('collapsiblockTitleCollapsed');
          targetElement.classList.add('collapsiblockContentCollapsed');
          targetElement.style.display = 'none';
          collapseButton.setAttribute('aria-expanded', false);
        } else {
          collapseButton.setAttribute('aria-expanded', true);
        }
      });
    },
  };
})(Drupal, window.Cookies, once);
