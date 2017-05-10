# Translate Extended
This plugins extends default behavior of the Rainlab's Translate plugin (http://octobercms.com/plugin/rainlab-translate) with following features:
 * detect browser language (optional)
 * display (and save into session) most preferred browser language instead of the default one (optional)
 * force browser language detection on every visit (optional)
 * prefix all routes with SEO-friendly locale short code (optional)

## Usage
The Translate Plugin have two scenarios of displaying translated content:

 * `http://website/lang/` will display the site in the language with 'lang' short code.
 * `http://website/` will display the site in the default language unless the user chooses preferred language
 
After installing the Translate Extended, when you visit home page URL:
 * plugin will get most preferred user languages (from browser settings) and try to match them with the enabled translations in Rainlab's Tranlsate plugin
 * if match is found, this preference will be saved into user session and displayed immediately 
 * if there is no match, website will be displayed in default language (from the Rainlab's Tranlsate plugin settings)
 * route will be SEO-friendly, automatically prefixed with proper language shortcode
 
After you change the route, it will be automatically prefixed with chosen language. If you manually enter the language URI in the address bar it will be saved in the user session and displayed immediately .

**Note:** by default, most preferred browser language is saved into user session on first visit. So next time user enters the website, locale won't be automatically detected anymore (it will be restored from the user session). There is possibility to force language detection on every visit in Translate Extended settings in backend area.

## Extended Locale Picker
There is custom locale picker component in the plugin. If you want to use locale prefixes in the URL, default locale picker from Rainlab's Translate probably won't work correctly. Extended Locale Picker "forces" correct locale prefixes, because it uses **href** attribute instead of ajax handler.

## Settings

Translate Extended have few setting available in the backend area under "Translate" category (so where Translate plugin settings reside)
 
## Language short codes
In order to work property Translate Extended needs correct language codes to be set in the Rainlab's Tranlsate plugin.
Language codes need to be identical with the ISO 639 Language Codes that are transmitted in the HTTP header "HTTP_ACCEPT_LANGUAGE".
http://www.metamodpro.com/browser-language-codes