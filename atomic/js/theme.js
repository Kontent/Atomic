/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

let BTN_LIGHT_CLASSES = 'fa-regular fa-sun';
let BTN_DARK_CLASSES = 'fa-solid fa-moon';

(() => {
    'use strict'

    const getStoredTheme = () => localStorage.getItem('theme')
    const setStoredTheme = theme => localStorage.setItem('theme', theme)

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
            return storedTheme
        }
        return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'
    }

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme)
    }

    setTheme(getPreferredTheme())

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        const storedTheme = getStoredTheme()
        if (storedTheme !== 'light' && storedTheme !== 'dark') {
            setTheme(getPreferredTheme())
        }
    })

    const showActiveTheme = (theme) => {
        let icon = document.querySelector('#themeBtn i')
        if (theme === 'dark') {
            icon.classList = BTN_DARK_CLASSES
        } else {
            icon.classList = BTN_LIGHT_CLASSES
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        showActiveTheme(getPreferredTheme())

        document.querySelector('#themeBtn').addEventListener('click', () => {
            let theme
            if (getPreferredTheme() === 'light') {
                theme = 'dark'
            } else {
                theme = 'light'
            }
            setStoredTheme(theme)
            setTheme(theme)
            showActiveTheme(theme, true)
        })
    })
})()


