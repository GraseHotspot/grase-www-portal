// Load the Translator from the composer version
export const Translator = require('../../vendor/willdurand/js-translation-bundle/Resources/js/translator');
// Make it global
global.Translator = Translator;
// Load the default config
require('./bazinga/translations/config');

// Load each language here. When we add more languages, we need to add them here.
require('./bazinga/translations/en');
