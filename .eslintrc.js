// http://eslint.org/docs/user-guide/configuring
module.exports = {
  settings: {
    react: {
      version: 'detect',
    },
  },
  root: true,
  parser: '@typescript-eslint/parser',
  parserOptions: {
    ecmaFeatures: {
      jsx: true,
    },
    sourceType: 'module',
  },
  env: {
    es6: true,
    browser: true,
    node: true,
  },
  rules: {
    'max-len': [
      'error',
      120,
      4,
      {
        ignoreTrailingComments: true,
        ignoreComments: true,
        code: 120,
        ignoreUrls: true,
        ignoreTemplateLiterals: true,
        ignoreStrings: true,
        ignoreRegExpLiterals: true,
      },
    ],
    'quote-props': 'off',
    semi: 'off',
    'arrow-parens': 'off',
    'comma-dangle': 'off',
    indent: 'off',
    'require-jsdoc': 'off',
    'operator-linebreak': 'off',
    'valid-jsdoc': 'off',
    'space-before-function-paren': 'off',
    'react/prop-types': 'off',
    'linebreak-style': 'off',
  },
  plugins: ['prettier', 'react'],
  globals: {
    JSX: 'readonly',
  },
  ignorePatterns: [
    'resources/js/components/UI/iconComponents/*.tsx',
    'resources/js/bootstrap.js',
    'node_modules',
    'vendor',
    'public',
    'docker',
    // Вырубаем для .js файлов, чтобы ошибки не пугали
    '**/*.js',
    '**/*.tsx',
    '**/*.ts',
    'webpack.mix.js',
    'webpack.css.js',
  ],
  extends: [
    'prettier',
    'eslint:recommended',
    'plugin:react/recommended',
    'plugin:react-hooks/recommended',
    'plugin:@typescript-eslint/recommended',
    'google',
  ],
}
