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
                    850: "#172033", // Adding custom slate-850 color for darker theme
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

            // Dark mode specific styles
            backgroundColor: {
                'dark-primary': 'var(--dark-primary, #172033)', // slate-850
                'dark-secondary': 'var(--dark-secondary, #1e293b)', // slate-800
                'dark-tertiary': 'var(--dark-tertiary, #334155)', // slate-700
            },
            textColor: {
                'dark-primary': 'var(--dark-text-primary, #f1f5f9)', // slate-100
                'dark-secondary': 'var(--dark-text-secondary, #cbd5e1)', // slate-300
                'dark-muted': 'var(--dark-text-muted, #94a3b8)', // slate-400
            },
            borderColor: {
                'dark-border': 'var(--dark-border, #334155)', // slate-700
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
