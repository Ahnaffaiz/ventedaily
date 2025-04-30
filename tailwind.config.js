import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

const colors = require("tailwindcss/colors");

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "node_modules/@frostui/tailwindcss/dist/*.js",
        "./public/assets/js/**/*.js", // Added to include your app.js
    ],

    darkMode: ["class", '[data-mode="dark"]'],

    theme: {
        container: {
            center: true,
        },

        fontFamily: {
            sans: ["Figtree", "sans-serif"],
        },

        extend: {
            colors: {
                primary: "#3e60d5",
                secondary: "#6c757d",
                success: "#47ad77",
                info: "#16a7e9",
                warning: "#ffc35a",
                danger: "#f15776",
                light: "#f2f2f7",
                dark: "#212529",

                gray: {
                    ...colors.gray,
                    800: "#313a46",
                },

                slate: {
                    ...colors.slate,
                    850: "#172033", // Custom slate-850 color for darker theme - matching app.css custom property
                },
            },

            minWidth: (theme) => ({
                ...theme("width"),
            }),

            maxWidth: (theme) => ({
                ...theme("width"),
            }),

            minHeight: (theme) => ({
                ...theme("height"),
            }),

            maxHeight: (theme) => ({
                ...theme("height"),
            }),

            // Dark mode specific styles - aligned with CSS custom properties in app.css
            backgroundColor: {
                'dark-primary': 'var(--dark-primary)', // Using CSS variable from app.css
                'dark-secondary': 'var(--dark-secondary)',
                'dark-tertiary': 'var(--dark-tertiary)',
            },
            textColor: {
                'dark-primary': 'var(--dark-text-primary)',
                'dark-secondary': 'var(--dark-text-secondary)',
                'dark-muted': 'var(--dark-text-muted)',
            },
            borderColor: {
                'dark-border': 'var(--dark-border)',
            },
        },
    },

    plugins: [
        forms,
        typography,
        require("@frostui/tailwindcss/plugin"),
        require("@tailwindcss/aspect-ratio"),
    ],
};
