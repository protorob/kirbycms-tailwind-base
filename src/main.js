import './main.css'

// Mobile menu toggle
const toggle = document.getElementById('menu-toggle')
const mobileMenu = document.getElementById('mobile-menu')

if (toggle && mobileMenu) {
  toggle.addEventListener('click', () => {
    const isOpen = !mobileMenu.classList.contains('pointer-events-none')
    mobileMenu.classList.toggle('opacity-0', isOpen)
    mobileMenu.classList.toggle('opacity-100', !isOpen)
    mobileMenu.classList.toggle('-translate-y-1', isOpen)
    mobileMenu.classList.toggle('translate-y-0', !isOpen)
    mobileMenu.classList.toggle('pointer-events-none', isOpen)
    mobileMenu.classList.toggle('pointer-events-auto', !isOpen)
  })
}
