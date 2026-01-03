import eslint from '@eslint/js';
import tseslint from 'typescript-eslint';
import pluginVue from 'eslint-plugin-vue';

import globals from 'globals';

export default tseslint.config(
    eslint.configs.recommended,
    ...tseslint.configs.recommended,
    ...pluginVue.configs['flat/recommended'],
    {
        files: ['**/*.vue', '**/*.ts', '**/*.js'],
        languageOptions: {
            parserOptions: {
                parser: tseslint.parser,
            },
            globals: {
                ...globals.browser,
            },
        },
    },
    {
        files: ['**/*.spec.ts'],
        rules: {
            '@typescript-eslint/no-explicit-any': 'off',
        },
    },
    {
        ignores: ['node_modules/**', 'vendor/**', 'dist/**', 'public/**'],
    }
);
