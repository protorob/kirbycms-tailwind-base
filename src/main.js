import './main.css'

// Mobile menu toggle
const toggle = document.getElementById('menu-toggle')
const mobileMenu = document.getElementById('mobile-menu')

if (toggle && mobileMenu) {
  toggle.addEventListener('click', () => {
    const isOpen = !mobileMenu.classList.contains('pointer-events-none')
    mobileMenu.classList.toggle('grid-rows-[0fr]', isOpen)
    mobileMenu.classList.toggle('grid-rows-[1fr]', !isOpen)
    mobileMenu.classList.toggle('opacity-0', isOpen)
    mobileMenu.classList.toggle('opacity-100', !isOpen)
    mobileMenu.classList.toggle('-translate-y-1', isOpen)
    mobileMenu.classList.toggle('translate-y-0', !isOpen)
    mobileMenu.classList.toggle('pointer-events-none', isOpen)
    mobileMenu.classList.toggle('pointer-events-auto', !isOpen)
  })
}

// Language dropdown (site/snippets/language-switcher.php) — desktop nav only;
// each instance is self-contained.
document.querySelectorAll('[data-lang-switcher]').forEach((switcher) => {
  const toggleBtn = switcher.querySelector('[data-lang-toggle]')
  const menu = switcher.querySelector('[data-lang-menu]')
  const chevron = switcher.querySelector('[data-lang-chevron]')
  if (!toggleBtn || !menu) return

  const closeMenu = () => {
    menu.classList.add('hidden')
    chevron?.classList.remove('rotate-180')
    toggleBtn.setAttribute('aria-expanded', 'false')
  }
  const openMenu = () => {
    menu.classList.remove('hidden')
    chevron?.classList.add('rotate-180')
    toggleBtn.setAttribute('aria-expanded', 'true')
  }

  toggleBtn.addEventListener('click', () => {
    menu.classList.contains('hidden') ? openMenu() : closeMenu()
  })
  document.addEventListener('click', (e) => {
    if (!switcher.contains(e.target)) closeMenu()
  })
  // Closing on focusout keeps keyboard users from tabbing past an open menu.
  switcher.addEventListener('focusout', (e) => {
    if (!switcher.contains(e.relatedTarget)) closeMenu()
  })
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !menu.classList.contains('hidden')) {
      closeMenu()
      toggleBtn.focus()
    }
  })
})
