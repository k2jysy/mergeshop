var POS =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	var Application = __webpack_require__(112);

	// sync config
	__webpack_require__(114);

	/**
	 * Services
	 */
	var EntitiesService = __webpack_require__(19);
	var HeaderService = __webpack_require__(116);
	var ModalService = __webpack_require__(58);
	var PopoverService = __webpack_require__(157);
	var PrintService = __webpack_require__(158);
	var TabsService = __webpack_require__(68);
	var ButtonsService = __webpack_require__(75);
	var NumpadService = __webpack_require__(159);

	/**
	 * SubApps
	 */
	var POSRouter = __webpack_require__(165);
	var SupportRouter = __webpack_require__(209);
	var PrintRouter = __webpack_require__(218);

	/**
	 * bootstrap Handlebars Helpers
	 */
	__webpack_require__(111);

	/**
	 * Create the app ...
	 */
	var app = new Application();

	/**
	 * ... add SubApps and Services
	 */
	app.entitiesService = new EntitiesService({
	  app: app
	});

	app.headerService = new HeaderService({
	  headerContainer : app.layout.getRegion('header'),
	  menuContainer   : app.layout.getRegion('menu')
	});

	app.posApp = new POSRouter({
	  container: app.layout.getRegion('main')
	});

	app.supportApp = new SupportRouter({
	  container: app.layout.getRegion('main')
	});

	app.printApp = new PrintRouter({
	  container: app.layout.getRegion('main')
	});

	app.modalService = new ModalService({
	  container: app.layout.getRegion('modal')
	});

	app.popoverService = new PopoverService();
	app.printService = new PrintService();
	app.tabsService = new TabsService();
	app.buttonsService = new ButtonsService();
	app.numpadService = new NumpadService();

	/**
	 * Attach app to window for third party plugins
	 */
	module.exports = app;

/***/ },
/* 1 */,
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var Radio = __webpack_require__(4);
	var _ = __webpack_require__(5);
	var $ = __webpack_require__(6);
	var hbs = __webpack_require__(7);
	var Utils = __webpack_require__(8);
	var polyglot = __webpack_require__(10);
	var debugFunction = __webpack_require__(13);
	var bb = __webpack_require__(16);
	var accounting = __webpack_require__(9);

	module.exports = Mn.Application.extend({

	  _initChannel: function () {
	    this.channelName = _.result(this, 'channelName') || 'global';
	    this.channel = _.result(this, 'channel') ||
	    Radio.channel(this.channelName);
	  },

	  _initDebug: function( debug ){
	    if( debug ){
	      debugFunction.enable('*');
	    }
	    Radio.DEBUG = debug;
	    console.info(
	      'Debugging is ' +
	      ( debug ? 'on' : 'off' )  +
	      ', visit http://woopos.com.au/docs/debugging'
	    );
	  },

	  _initOptions: function( payload ){
	    payload = payload || {};

	    // templates
	    hbs.Templates = payload.templates || {};

	    // polyglot
	    polyglot.extend( payload.i18n );

	    // options
	    this.options = payload.params || {};

	    // debug
	    this._initDebug( this.options.debug );

	    // emulateHTTP
	    bb.emulateHTTP = this.options.emulateHTTP === true;

	    // bootstrap accounting settings
	    accounting.settings = this.options.accounting;
	  },

	  /**
	   * todo: handle errors
	   * @param options
	   */
	  start: function( options ){
	    var self = this;
	    $.getJSON(
	      options.ajaxurl, {
	        action: options.action || 'wc_pos_payload',
	        security: options.nonce
	      }, function( payload ){
	        self._initOptions( payload );
	        Mn.Application.prototype.start.call(self, payload);
	      }
	    );
	  },

	  set: function( path, value ){
	    _.set( this, path, value );
	  },

	  // namespace prefix for WP Admin
	  namespace: function( str ){
	    var prefix = window.adminpage ? 'wc_pos-' : '' ;
	    return prefix + str;
	  },

	  // extend app for third party plugins
	  debug: debugFunction,
	  polyglot: polyglot,
	  Utils: Utils

	});

	/**
	 * Custom Template Access
	 **/
	Mn.TemplateCache.prototype.loadTemplate = function(templateId){
	  return _.get( hbs.Templates, templateId.split('.'), $(templateId).html() );
	};

	Mn.TemplateCache.prototype.compileTemplate = function(rawTemplate) {
	  return hbs.compile(rawTemplate);
	};

/***/ },
/* 3 */
/***/ function(module, exports) {

	module.exports = Marionette;

/***/ },
/* 4 */
/***/ function(module, exports) {

	module.exports = Backbone.Radio;

/***/ },
/* 5 */
/***/ function(module, exports) {

	module.exports = _;

/***/ },
/* 6 */
/***/ function(module, exports) {

	module.exports = jQuery;

/***/ },
/* 7 */
/***/ function(module, exports) {

	module.exports = Handlebars;

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var accounting = __webpack_require__(9);
	var _ = __webpack_require__(5);
	var Utils = {};

	/**
	 * Using the same function as Woo: /assets/js/admin/round.js
	 * PHP_ROUND_HALF_EVEN should be the default?!
	 * @param value
	 * @param precision
	 * @param mode
	 * @returns {number}
	 */
	/* jshint -W018, -W071, -W074 */
	Utils.round = function(value, precision, mode) {
	  // http://kevin.vanzonneveld.net
	  // +   original by: Philip Peterson
	  // +    revised by: Onno Marsman
	  // +      input by: Greenseed
	  // +    revised by: T.Wild
	  // +      input by: meo
	  // +      input by: William
	  // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	  // +      input by: Josep Sanz (http://www.ws3.es/)
	  // +    revised by: Rafał Kukawski (http://blog.kukawski.pl/)
	  // %        note 1: Great work. Ideas for improvement:
	  // %        note 1:  - code more compliant with developer guidelines
	  // %        note 1:  - for implementing PHP constant arguments look at
	  // %        note 1:  the pathinfo() function, it offers the greatest
	  // %        note 1:  flexibility & compatibility possible
	  // *     example 1: round(1241757, -3);
	  // *     returns 1: 1242000
	  // *     example 2: round(3.6);
	  // *     returns 2: 4
	  // *     example 3: round(2.835, 2);
	  // *     returns 3: 2.84
	  // *     example 4: round(1.1749999999999, 2);
	  // *     returns 4: 1.17
	  // *     example 5: round(58551.799999999996, 2);
	  // *     returns 5: 58551.8

	  //
	  //mode = mode || 'PHP_ROUND_HALF_EVEN';

	  if( !_.isFinite( parseInt(precision, 10) ) ) {
	    precision = accounting.settings.currency.precision;
	  }

	  var m, f, isHalf, sgn; // helper variables
	  //precision |= 0; // making sure precision is integer
	  m = Math.pow(10, precision);
	  value *= m;
	  sgn = (value > 0) | -(value < 0); // sign of the number
	  isHalf = value % 1 === 0.5 * sgn;
	  f = Math.floor(value);

	  if (isHalf) {
	    switch (mode) {
	      case '2':
	      case 'PHP_ROUND_HALF_DOWN':
	        value = f + (sgn < 0); // rounds .5 toward zero
	        break;
	      case '3':
	      case 'PHP_ROUND_HALF_EVEN':
	        value = f + (f % 2 * sgn); // rouds .5 towards the next even integer
	        break;
	      case '4':
	      case 'PHP_ROUND_HALF_ODD':
	        value = f + !(f % 2); // rounds .5 towards the next odd integer
	        break;
	      default:
	        value = f + (sgn > 0); // rounds .5 away from zero
	    }
	  }

	  return (isHalf ? value : Math.round(value)) / m;
	};
	/* jshint +W018, +W071, +W074 */

	/**
	 * Number of significant decimal places
	 */
	Utils.decimalPlaces = function(num){
	  return ((+num).toFixed(4)).replace(/^-?\d*\.?|0+$/g, '').length;
	};

	/**
	 *
	 */
	Utils.unformat = function( num ) {
	  return accounting.unformat( num, accounting.settings.number.decimal );
	};

	/**
	 *
	 */
	Utils.formatNumber = function( num, precision ) {
	  if( precision === 'auto' ) {
	    precision = Utils.decimalPlaces(num);
	  }
	  if( !_.isFinite( parseInt(precision, 10) ) ) {
	    precision = accounting.settings.currency.precision;
	  }
	  return accounting.formatNumber(num, precision);
	};

	/**
	 *
	 */
	Utils.formatMoney = function( num, precision ) {
	  if( precision === 'auto' ) {
	    precision = Utils.decimalPlaces(num);
	  }
	  if( !_.isFinite( parseInt(precision, 10) ) ) {
	    precision = accounting.settings.currency.precision;
	  }
	  // round the number to even
	  num = Utils.round(num, precision);
	  return accounting.formatMoney(num);
	};

	/**
	 *
	 */
	Utils.isPositiveInteger = function( num, allowZero ){
	  var n = ~~Number(num);
	  if(allowZero) {
	    return String(n) === num && n >= 0;
	  } else {
	    return String(n) === num && n > 0;
	  }
	};

	/**
	 * Parse error messages from the server
	 */
	Utils.parseErrorResponse = function( jqXHR ){
	  var resp = jqXHR.responseJSON;
	  if( resp.errors ){
	    return resp.errors[0].message;
	  }

	  return jqXHR.responseText;
	};

	/**
	 * returns the variable type
	 * http://wp.me/pQpop-JM
	 *
	 *
	toType({a: 4}); //"object"
	toType([1, 2, 3]); //"array"
	(function() {console.log(toType(arguments))})(); //arguments
	toType(new ReferenceError); //"error"
	toType(new Date); //"date"
	toType(/a-z/); //"regexp"
	toType(Math); //"math"
	toType(JSON); //"json"
	toType(new Number(4)); //"number"
	toType(new String("abc")); //"string"
	toType(new Boolean(true)); //"boolean"

	 */
	Utils.toType = function(obj) {
	  return ({}).toString.call(obj).match(/\s([a-z|A-Z]+)/)[1].toLowerCase();
	};

	module.exports = Utils;

/***/ },
/* 9 */
/***/ function(module, exports) {

	module.exports = accounting;

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var Polyglot = __webpack_require__(11);
	module.exports = new Polyglot();

/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	// Added for convenience in the Node environment.
	// The meat and potatoes exist in ./lib/polyglot.js.
	module.exports = __webpack_require__(12);


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;//     (c) 2012 Airbnb, Inc.
	//
	//     polyglot.js may be freely distributed under the terms of the BSD
	//     license. For all licensing information, details, and documention:
	//     http://airbnb.github.com/polyglot.js
	//
	//
	// Polyglot.js is an I18n helper library written in JavaScript, made to
	// work both in the browser and in Node. It provides a simple solution for
	// interpolation and pluralization, based off of Airbnb's
	// experience adding I18n functionality to its Backbone.js and Node apps.
	//
	// Polylglot is agnostic to your translation backend. It doesn't perform any
	// translation; it simply gives you a way to manage translated phrases from
	// your client- or server-side JavaScript application.
	//


	(function(root, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = function() {
	      return factory(root);
	    }.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports === 'object') {
	    module.exports = factory(root);
	  } else {
	    root.Polyglot = factory(root);
	  }
	}(this, function(root) {
	  'use strict';

	  // ### Polyglot class constructor
	  function Polyglot(options) {
	    options = options || {};
	    this.phrases = {};
	    this.extend(options.phrases || {});
	    this.currentLocale = options.locale || 'en';
	    this.allowMissing = !!options.allowMissing;
	    this.warn = options.warn || warn;
	  }

	  // ### Version
	  Polyglot.VERSION = '0.4.3';

	  // ### polyglot.locale([locale])
	  //
	  // Get or set locale. Internally, Polyglot only uses locale for pluralization.
	  Polyglot.prototype.locale = function(newLocale) {
	    if (newLocale) this.currentLocale = newLocale;
	    return this.currentLocale;
	  };

	  // ### polyglot.extend(phrases)
	  //
	  // Use `extend` to tell Polyglot how to translate a given key.
	  //
	  //     polyglot.extend({
	  //       "hello": "Hello",
	  //       "hello_name": "Hello, %{name}"
	  //     });
	  //
	  // The key can be any string.  Feel free to call `extend` multiple times;
	  // it will override any phrases with the same key, but leave existing phrases
	  // untouched.
	  //
	  // It is also possible to pass nested phrase objects, which get flattened
	  // into an object with the nested keys concatenated using dot notation.
	  //
	  //     polyglot.extend({
	  //       "nav": {
	  //         "hello": "Hello",
	  //         "hello_name": "Hello, %{name}",
	  //         "sidebar": {
	  //           "welcome": "Welcome"
	  //         }
	  //       }
	  //     });
	  //
	  //     console.log(polyglot.phrases);
	  //     // {
	  //     //   'nav.hello': 'Hello',
	  //     //   'nav.hello_name': 'Hello, %{name}',
	  //     //   'nav.sidebar.welcome': 'Welcome'
	  //     // }
	  //
	  // `extend` accepts an optional second argument, `prefix`, which can be used
	  // to prefix every key in the phrases object with some string, using dot
	  // notation.
	  //
	  //     polyglot.extend({
	  //       "hello": "Hello",
	  //       "hello_name": "Hello, %{name}"
	  //     }, "nav");
	  //
	  //     console.log(polyglot.phrases);
	  //     // {
	  //     //   'nav.hello': 'Hello',
	  //     //   'nav.hello_name': 'Hello, %{name}'
	  //     // }
	  //
	  // This feature is used internally to support nested phrase objects.
	  Polyglot.prototype.extend = function(morePhrases, prefix) {
	    var phrase;

	    for (var key in morePhrases) {
	      if (morePhrases.hasOwnProperty(key)) {
	        phrase = morePhrases[key];
	        if (prefix) key = prefix + '.' + key;
	        if (typeof phrase === 'object') {
	          this.extend(phrase, key);
	        } else {
	          this.phrases[key] = phrase;
	        }
	      }
	    }
	  };

	  // ### polyglot.clear()
	  //
	  // Clears all phrases. Useful for special cases, such as freeing
	  // up memory if you have lots of phrases but no longer need to
	  // perform any translation. Also used internally by `replace`.
	  Polyglot.prototype.clear = function() {
	    this.phrases = {};
	  };

	  // ### polyglot.replace(phrases)
	  //
	  // Completely replace the existing phrases with a new set of phrases.
	  // Normally, just use `extend` to add more phrases, but under certain
	  // circumstances, you may want to make sure no old phrases are lying around.
	  Polyglot.prototype.replace = function(newPhrases) {
	    this.clear();
	    this.extend(newPhrases);
	  };


	  // ### polyglot.t(key, options)
	  //
	  // The most-used method. Provide a key, and `t` will return the
	  // phrase.
	  //
	  //     polyglot.t("hello");
	  //     => "Hello"
	  //
	  // The phrase value is provided first by a call to `polyglot.extend()` or
	  // `polyglot.replace()`.
	  //
	  // Pass in an object as the second argument to perform interpolation.
	  //
	  //     polyglot.t("hello_name", {name: "Spike"});
	  //     => "Hello, Spike"
	  //
	  // If you like, you can provide a default value in case the phrase is missing.
	  // Use the special option key "_" to specify a default.
	  //
	  //     polyglot.t("i_like_to_write_in_language", {
	  //       _: "I like to write in %{language}.",
	  //       language: "JavaScript"
	  //     });
	  //     => "I like to write in JavaScript."
	  //
	  Polyglot.prototype.t = function(key, options) {
	    var phrase, result;
	    options = options == null ? {} : options;
	    // allow number as a pluralization shortcut
	    if (typeof options === 'number') {
	      options = {smart_count: options};
	    }
	    if (typeof this.phrases[key] === 'string') {
	      phrase = this.phrases[key];
	    } else if (typeof options._ === 'string') {
	      phrase = options._;
	    } else if (this.allowMissing) {
	      phrase = key;
	    } else {
	      this.warn('Missing translation for key: "'+key+'"');
	      result = key;
	    }
	    if (typeof phrase === 'string') {
	      options = clone(options);
	      result = choosePluralForm(phrase, this.currentLocale, options.smart_count);
	      result = interpolate(result, options);
	    }
	    return result;
	  };


	  // ### polyglot.has(key)
	  //
	  // Check if polyglot has a translation for given key
	  Polyglot.prototype.has = function(key) {
	    return key in this.phrases;
	  };


	  // #### Pluralization methods
	  // The string that separates the different phrase possibilities.
	  var delimeter = '||||';

	  // Mapping from pluralization group plural logic.
	  var pluralTypes = {
	    chinese:   function(n) { return 0; },
	    german:    function(n) { return n !== 1 ? 1 : 0; },
	    french:    function(n) { return n > 1 ? 1 : 0; },
	    russian:   function(n) { return n % 10 === 1 && n % 100 !== 11 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2; },
	    czech:     function(n) { return (n === 1) ? 0 : (n >= 2 && n <= 4) ? 1 : 2; },
	    polish:    function(n) { return (n === 1 ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2); },
	    icelandic: function(n) { return (n % 10 !== 1 || n % 100 === 11) ? 1 : 0; }
	  };

	  // Mapping from pluralization group to individual locales.
	  var pluralTypeToLanguages = {
	    chinese:   ['fa', 'id', 'ja', 'ko', 'lo', 'ms', 'th', 'tr', 'zh'],
	    german:    ['da', 'de', 'en', 'es', 'fi', 'el', 'he', 'hu', 'it', 'nl', 'no', 'pt', 'sv'],
	    french:    ['fr', 'tl', 'pt-br'],
	    russian:   ['hr', 'ru'],
	    czech:     ['cs'],
	    polish:    ['pl'],
	    icelandic: ['is']
	  };

	  function langToTypeMap(mapping) {
	    var type, langs, l, ret = {};
	    for (type in mapping) {
	      if (mapping.hasOwnProperty(type)) {
	        langs = mapping[type];
	        for (l in langs) {
	          ret[langs[l]] = type;
	        }
	      }
	    }
	    return ret;
	  }

	  // Trim a string.
	  function trim(str){
	    var trimRe = /^\s+|\s+$/g;
	    return str.replace(trimRe, '');
	  }

	  // Based on a phrase text that contains `n` plural forms separated
	  // by `delimeter`, a `locale`, and a `count`, choose the correct
	  // plural form, or none if `count` is `null`.
	  function choosePluralForm(text, locale, count){
	    var ret, texts, chosenText;
	    if (count != null && text) {
	      texts = text.split(delimeter);
	      chosenText = texts[pluralTypeIndex(locale, count)] || texts[0];
	      ret = trim(chosenText);
	    } else {
	      ret = text;
	    }
	    return ret;
	  }

	  function pluralTypeName(locale) {
	    var langToPluralType = langToTypeMap(pluralTypeToLanguages);
	    return langToPluralType[locale] || langToPluralType.en;
	  }

	  function pluralTypeIndex(locale, count) {
	    return pluralTypes[pluralTypeName(locale)](count);
	  }

	  // ### interpolate
	  //
	  // Does the dirty work. Creates a `RegExp` object for each
	  // interpolation placeholder.
	  function interpolate(phrase, options) {
	    for (var arg in options) {
	      if (arg !== '_' && options.hasOwnProperty(arg)) {
	        // We create a new `RegExp` each time instead of using a more-efficient
	        // string replace so that the same argument can be replaced multiple times
	        // in the same phrase.
	        phrase = phrase.replace(new RegExp('%\\{'+arg+'\\}', 'g'), options[arg]);
	      }
	    }
	    return phrase;
	  }

	  // ### warn
	  //
	  // Provides a warning in the console if a phrase key is missing.
	  function warn(message) {
	    root.console && root.console.warn && root.console.warn('WARNING: ' + message);
	  }

	  // ### clone
	  //
	  // Clone an object.
	  function clone(source) {
	    var ret = {};
	    for (var prop in source) {
	      ret[prop] = source[prop];
	    }
	    return ret;
	  }

	  return Polyglot;
	}));


/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	
	/**
	 * This is the web browser implementation of `debug()`.
	 *
	 * Expose `debug()` as the module.
	 */

	exports = module.exports = __webpack_require__(14);
	exports.log = log;
	exports.formatArgs = formatArgs;
	exports.save = save;
	exports.load = load;
	exports.useColors = useColors;
	exports.storage = 'undefined' != typeof chrome
	               && 'undefined' != typeof chrome.storage
	                  ? chrome.storage.local
	                  : localstorage();

	/**
	 * Colors.
	 */

	exports.colors = [
	  'lightseagreen',
	  'forestgreen',
	  'goldenrod',
	  'dodgerblue',
	  'darkorchid',
	  'crimson'
	];

	/**
	 * Currently only WebKit-based Web Inspectors, Firefox >= v31,
	 * and the Firebug extension (any Firefox version) are known
	 * to support "%c" CSS customizations.
	 *
	 * TODO: add a `localStorage` variable to explicitly enable/disable colors
	 */

	function useColors() {
	  // is webkit? http://stackoverflow.com/a/16459606/376773
	  return ('WebkitAppearance' in document.documentElement.style) ||
	    // is firebug? http://stackoverflow.com/a/398120/376773
	    (window.console && (console.firebug || (console.exception && console.table))) ||
	    // is firefox >= v31?
	    // https://developer.mozilla.org/en-US/docs/Tools/Web_Console#Styling_messages
	    (navigator.userAgent.toLowerCase().match(/firefox\/(\d+)/) && parseInt(RegExp.$1, 10) >= 31);
	}

	/**
	 * Map %j to `JSON.stringify()`, since no Web Inspectors do that by default.
	 */

	exports.formatters.j = function(v) {
	  return JSON.stringify(v);
	};


	/**
	 * Colorize log arguments if enabled.
	 *
	 * @api public
	 */

	function formatArgs() {
	  var args = arguments;
	  var useColors = this.useColors;

	  args[0] = (useColors ? '%c' : '')
	    + this.namespace
	    + (useColors ? ' %c' : ' ')
	    + args[0]
	    + (useColors ? '%c ' : ' ')
	    + '+' + exports.humanize(this.diff);

	  if (!useColors) return args;

	  var c = 'color: ' + this.color;
	  args = [args[0], c, 'color: inherit'].concat(Array.prototype.slice.call(args, 1));

	  // the final "%c" is somewhat tricky, because there could be other
	  // arguments passed either before or after the %c, so we need to
	  // figure out the correct index to insert the CSS into
	  var index = 0;
	  var lastC = 0;
	  args[0].replace(/%[a-z%]/g, function(match) {
	    if ('%%' === match) return;
	    index++;
	    if ('%c' === match) {
	      // we only are interested in the *last* %c
	      // (the user may have provided their own)
	      lastC = index;
	    }
	  });

	  args.splice(lastC, 0, c);
	  return args;
	}

	/**
	 * Invokes `console.log()` when available.
	 * No-op when `console.log` is not a "function".
	 *
	 * @api public
	 */

	function log() {
	  // this hackery is required for IE8/9, where
	  // the `console.log` function doesn't have 'apply'
	  return 'object' === typeof console
	    && console.log
	    && Function.prototype.apply.call(console.log, console, arguments);
	}

	/**
	 * Save `namespaces`.
	 *
	 * @param {String} namespaces
	 * @api private
	 */

	function save(namespaces) {
	  try {
	    if (null == namespaces) {
	      exports.storage.removeItem('debug');
	    } else {
	      exports.storage.debug = namespaces;
	    }
	  } catch(e) {}
	}

	/**
	 * Load `namespaces`.
	 *
	 * @return {String} returns the previously persisted debug modes
	 * @api private
	 */

	function load() {
	  var r;
	  try {
	    r = exports.storage.debug;
	  } catch(e) {}
	  return r;
	}

	/**
	 * Enable namespaces listed in `localStorage.debug` initially.
	 */

	exports.enable(load());

	/**
	 * Localstorage attempts to return the localstorage.
	 *
	 * This is necessary because safari throws
	 * when a user disables cookies/localstorage
	 * and you attempt to access it.
	 *
	 * @return {LocalStorage}
	 * @api private
	 */

	function localstorage(){
	  try {
	    return window.localStorage;
	  } catch (e) {}
	}


/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	
	/**
	 * This is the common logic for both the Node.js and web browser
	 * implementations of `debug()`.
	 *
	 * Expose `debug()` as the module.
	 */

	exports = module.exports = debug;
	exports.coerce = coerce;
	exports.disable = disable;
	exports.enable = enable;
	exports.enabled = enabled;
	exports.humanize = __webpack_require__(15);

	/**
	 * The currently active debug mode names, and names to skip.
	 */

	exports.names = [];
	exports.skips = [];

	/**
	 * Map of special "%n" handling functions, for the debug "format" argument.
	 *
	 * Valid key names are a single, lowercased letter, i.e. "n".
	 */

	exports.formatters = {};

	/**
	 * Previously assigned color.
	 */

	var prevColor = 0;

	/**
	 * Previous log timestamp.
	 */

	var prevTime;

	/**
	 * Select a color.
	 *
	 * @return {Number}
	 * @api private
	 */

	function selectColor() {
	  return exports.colors[prevColor++ % exports.colors.length];
	}

	/**
	 * Create a debugger with the given `namespace`.
	 *
	 * @param {String} namespace
	 * @return {Function}
	 * @api public
	 */

	function debug(namespace) {

	  // define the `disabled` version
	  function disabled() {
	  }
	  disabled.enabled = false;

	  // define the `enabled` version
	  function enabled() {

	    var self = enabled;

	    // set `diff` timestamp
	    var curr = +new Date();
	    var ms = curr - (prevTime || curr);
	    self.diff = ms;
	    self.prev = prevTime;
	    self.curr = curr;
	    prevTime = curr;

	    // add the `color` if not set
	    if (null == self.useColors) self.useColors = exports.useColors();
	    if (null == self.color && self.useColors) self.color = selectColor();

	    var args = Array.prototype.slice.call(arguments);

	    args[0] = exports.coerce(args[0]);

	    if ('string' !== typeof args[0]) {
	      // anything else let's inspect with %o
	      args = ['%o'].concat(args);
	    }

	    // apply any `formatters` transformations
	    var index = 0;
	    args[0] = args[0].replace(/%([a-z%])/g, function(match, format) {
	      // if we encounter an escaped % then don't increase the array index
	      if (match === '%%') return match;
	      index++;
	      var formatter = exports.formatters[format];
	      if ('function' === typeof formatter) {
	        var val = args[index];
	        match = formatter.call(self, val);

	        // now we need to remove `args[index]` since it's inlined in the `format`
	        args.splice(index, 1);
	        index--;
	      }
	      return match;
	    });

	    if ('function' === typeof exports.formatArgs) {
	      args = exports.formatArgs.apply(self, args);
	    }
	    var logFn = enabled.log || exports.log || console.log.bind(console);
	    logFn.apply(self, args);
	  }
	  enabled.enabled = true;

	  var fn = exports.enabled(namespace) ? enabled : disabled;

	  fn.namespace = namespace;

	  return fn;
	}

	/**
	 * Enables a debug mode by namespaces. This can include modes
	 * separated by a colon and wildcards.
	 *
	 * @param {String} namespaces
	 * @api public
	 */

	function enable(namespaces) {
	  exports.save(namespaces);

	  var split = (namespaces || '').split(/[\s,]+/);
	  var len = split.length;

	  for (var i = 0; i < len; i++) {
	    if (!split[i]) continue; // ignore empty strings
	    namespaces = split[i].replace(/\*/g, '.*?');
	    if (namespaces[0] === '-') {
	      exports.skips.push(new RegExp('^' + namespaces.substr(1) + '$'));
	    } else {
	      exports.names.push(new RegExp('^' + namespaces + '$'));
	    }
	  }
	}

	/**
	 * Disable debug output.
	 *
	 * @api public
	 */

	function disable() {
	  exports.enable('');
	}

	/**
	 * Returns true if the given mode name is enabled, false otherwise.
	 *
	 * @param {String} name
	 * @return {Boolean}
	 * @api public
	 */

	function enabled(name) {
	  var i, len;
	  for (i = 0, len = exports.skips.length; i < len; i++) {
	    if (exports.skips[i].test(name)) {
	      return false;
	    }
	  }
	  for (i = 0, len = exports.names.length; i < len; i++) {
	    if (exports.names[i].test(name)) {
	      return true;
	    }
	  }
	  return false;
	}

	/**
	 * Coerce `val`.
	 *
	 * @param {Mixed} val
	 * @return {Mixed}
	 * @api private
	 */

	function coerce(val) {
	  if (val instanceof Error) return val.stack || val.message;
	  return val;
	}


/***/ },
/* 15 */
/***/ function(module, exports) {

	/**
	 * Helpers.
	 */

	var s = 1000;
	var m = s * 60;
	var h = m * 60;
	var d = h * 24;
	var y = d * 365.25;

	/**
	 * Parse or format the given `val`.
	 *
	 * Options:
	 *
	 *  - `long` verbose formatting [false]
	 *
	 * @param {String|Number} val
	 * @param {Object} options
	 * @return {String|Number}
	 * @api public
	 */

	module.exports = function(val, options){
	  options = options || {};
	  if ('string' == typeof val) return parse(val);
	  return options.long
	    ? long(val)
	    : short(val);
	};

	/**
	 * Parse the given `str` and return milliseconds.
	 *
	 * @param {String} str
	 * @return {Number}
	 * @api private
	 */

	function parse(str) {
	  str = '' + str;
	  if (str.length > 10000) return;
	  var match = /^((?:\d+)?\.?\d+) *(milliseconds?|msecs?|ms|seconds?|secs?|s|minutes?|mins?|m|hours?|hrs?|h|days?|d|years?|yrs?|y)?$/i.exec(str);
	  if (!match) return;
	  var n = parseFloat(match[1]);
	  var type = (match[2] || 'ms').toLowerCase();
	  switch (type) {
	    case 'years':
	    case 'year':
	    case 'yrs':
	    case 'yr':
	    case 'y':
	      return n * y;
	    case 'days':
	    case 'day':
	    case 'd':
	      return n * d;
	    case 'hours':
	    case 'hour':
	    case 'hrs':
	    case 'hr':
	    case 'h':
	      return n * h;
	    case 'minutes':
	    case 'minute':
	    case 'mins':
	    case 'min':
	    case 'm':
	      return n * m;
	    case 'seconds':
	    case 'second':
	    case 'secs':
	    case 'sec':
	    case 's':
	      return n * s;
	    case 'milliseconds':
	    case 'millisecond':
	    case 'msecs':
	    case 'msec':
	    case 'ms':
	      return n;
	  }
	}

	/**
	 * Short format for `ms`.
	 *
	 * @param {Number} ms
	 * @return {String}
	 * @api private
	 */

	function short(ms) {
	  if (ms >= d) return Math.round(ms / d) + 'd';
	  if (ms >= h) return Math.round(ms / h) + 'h';
	  if (ms >= m) return Math.round(ms / m) + 'm';
	  if (ms >= s) return Math.round(ms / s) + 's';
	  return ms + 'ms';
	}

	/**
	 * Long format for `ms`.
	 *
	 * @param {Number} ms
	 * @return {String}
	 * @api private
	 */

	function long(ms) {
	  return plural(ms, d, 'day')
	    || plural(ms, h, 'hour')
	    || plural(ms, m, 'minute')
	    || plural(ms, s, 'second')
	    || ms + ' ms';
	}

	/**
	 * Pluralization helper.
	 */

	function plural(ms, n, name) {
	  if (ms < n) return;
	  if (ms < n * 1.5) return Math.floor(ms / n) + ' ' + name;
	  return Math.ceil(ms / n) + ' ' + name + 's';
	}


/***/ },
/* 16 */
/***/ function(module, exports) {

	module.exports = Backbone;

/***/ },
/* 17 */,
/* 18 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var app = __webpack_require__(2);

	module.exports = app.prototype.LayoutView = Mn.LayoutView.extend({

	  working: function( action ) {
	    if (action === 'start') {
	      this.$el.addClass('working');
	    } else {
	      this.$el.removeClass('working');
	    }
	  }

	});

/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var Service = __webpack_require__(20);
	var Products = __webpack_require__(21);
	var Orders = __webpack_require__(46);
	var Cart = __webpack_require__(48);
	var Customers = __webpack_require__(50);
	var Coupons = __webpack_require__(52);
	var Settings = __webpack_require__(54);
	var SettingsCollection = __webpack_require__(55);
	var Gateways = __webpack_require__(56);
	var FilteredCollection = __webpack_require__(35);
	var debug = __webpack_require__(13)('entities');
	var App = __webpack_require__(2);
	var _ = __webpack_require__(5);
	var storage = global.localStorage || window.localStorage;
	var JSON = global.JSON || window.JSON;

	var EntitiesService = Service.extend({
	  channelName: 'entities',

	  initialize: function() {
	    this.channel.reply('get', this.get, this);
	    this.channel.reply('set', this.set, this);
	    this.channel.reply('remove', this.remove, this);
	    this.channel.reply('set:filter', this.setFilter, this);
	  },

	  collections: {
	    products  : Products,
	    orders    : Orders,
	    cart      : Cart,
	    customers : Customers,
	    coupons   : Coupons,
	    gateways  : Gateways,
	    settings  : SettingsCollection
	  },

	  getMethods: {
	    collection  : 'getCollection',
	    model       : 'getModel',
	    filtered    : 'getFiltered',
	    option      : 'getOption',
	    settings    : 'getSettings',
	    localStorage: 'getLocalStorage'
	  },

	  setMethods: {
	    localStorage: 'setLocalStorage'
	  },

	  get: function(options){
	    options = options || {};
	    var method = this.getMethods[options.type];
	    if( this[method] ){
	      return this[method](options);
	    }
	    debug('request needs a type, eg: "collection" or "option"');
	  },

	  set: function(options){
	    options = options || {};
	    var method = this.setMethods[options.type];
	    if( this[method] ){
	      return this[method](options);
	    }
	    debug('set needs a type, eg: "localStorage"');
	  },

	  /**
	   * init a new collection, attach to this and return a reference
	   */
	  attach: function(options){
	    var type = options.type === 'model' ? 'models' : 'collections',
	        prop = '_' + options.name;
	    if( this[type].hasOwnProperty(options.name) ){
	      this[prop] = new this[type][options.name]([], options);
	    }
	    return this[prop];
	  },

	  /**
	   * return a reference to the collection
	   */
	  getCollection: function(options){
	    var prop = '_' + options.name;
	    if( options.init ) {
	      return new this.collections[options.name]([], options);
	    }
	    return ( this[prop] || this.attach(options) );
	  },

	  getAllCollections: function(){
	    return _.reduce( this.collections, function(result, col, key){
	      result[key] = this.getCollection({ name: key });
	      return result;
	    }, {}, this);
	  },

	  getModel: function(options){
	    var prop = '_' + options.name;
	    if( options.init ) {
	      return new this.models[options.name]([], options);
	    }
	    return ( this[prop] || this.attach(options) );
	  },

	  /**
	   * return a filtered collection and attach to this
	   */
	  getFiltered: function(options){
	    var prop = '_' + options.name;
	    var filteredProp = '_filtered' + options.name;
	    if( this[filteredProp] ){ return this[filteredProp]; }
	    if( !this[prop] ){ this.attach(options); }

	    this[filteredProp] = new FilteredCollection(this[prop], options);
	    return this[filteredProp];
	  },

	  /**
	   * return an option set during app.start(options)
	   */
	  getOption: function(options){
	    return this.app.getOption(options.name);
	  },

	  /**
	   * settings are App options that can be changed by the user
	   * eg: HotKeys are bootstrapped as start options, but also
	   * can be updated through the POS
	   */
	  getSettings: function(options){
	    var option = this.app.getOption(options.name);
	    return new Settings(option);
	  },

	  setFilter: function(options){
	    options = options || {};
	    var filteredProp = '_filtered' + options.name;
	    if( this[filteredProp] ){
	      this[filteredProp].filterBy('search', options.filter);
	    }
	  },

	  serialize: function(value){
	    return JSON.stringify(value);
	  },

	  deserialize: function(value){
	    try { value = JSON.parse(value); }
	    catch(e) { debug(e); }
	    return value || undefined;
	  },

	  getLocalStorage: function(options){
	    options = options || {};
	    var data = storage.getItem('wc_pos_' + options.name);
	    var obj = this.deserialize(data);
	    if(options.key && obj && obj[options.key]){
	      return obj[options.key];
	    }
	    return obj;
	  },

	  setLocalStorage: function(options){
	    options = options || {};
	    var data = options.data;
	    var old = this.getLocalStorage({name: options.name});
	    if( _.isObject(old) && _.isObject(data) ){
	      data = _.extend(old, data);
	    }
	    storage.setItem('wc_pos_' + options.name, this.serialize(data));
	  },

	  remove: function(options){
	    options = options || {};
	    if(options.type === 'localStorage' && options.name && options.key){
	      var data = this.getLocalStorage({name: options.name});
	      delete data[options.key];
	      storage.setItem('wc_pos_' + options.name, JSON.stringify(data));
	    } else {
	      storage.removeItem('wc_pos_' + options.name);
	    }
	  },

	  idbCollections: function(){
	    return _.reduce( this.getAllCollections(), function(result, col, key){
	      if( col instanceof App.IndexedDBCollection ){
	        result[key] = col;
	      }
	      return result;
	    }, {}, this);
	  }

	});

	module.exports = EntitiesService;
	App.prototype.set('Entities.Service', EntitiesService);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var Radio = __webpack_require__(4);
	var app = __webpack_require__(2);
	var _ = __webpack_require__(5);

	module.exports = app.prototype.Service = Mn.Object.extend({
	  constructor: function(options) {
	    options = options || {};

	    if (this.channelName) {
	      this.channel = Radio.channel(_.result(this, 'channelName'));
	    }

	    // add reference to the app, like old Marionette.Module
	    if(options.app){
	      this.app = options.app;
	    }

	    Mn.Object.apply(this, arguments);
	  },

	  start: function(){
	    this.triggerMethod('before:start');
	    this._isStarted = true;
	    this.triggerMethod('start');
	  },

	  stop: function(){
	    this.triggerMethod('before:stop');
	    this._isStarted = false;
	    this.triggerMethod('stop');
	  },

	  isStarted: function(){
	    return this._isStarted === true;
	  }

	});

/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	var DualCollection = __webpack_require__(22);
	var Model = __webpack_require__(28);

	module.exports = DualCollection.extend({
	  model: Model,
	  name: 'products'
	});

/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Dual Collection makes sure the data locally and the data on the server
	 * stay in sync.
	 */

	var Backbone = __webpack_require__(16);
	var Radio = Backbone.Radio;
	var debug = __webpack_require__(13)('dualCollection');
	var IDBCollection = __webpack_require__(23);
	var app = __webpack_require__(2);
	var _ = __webpack_require__(5);
	var $ = __webpack_require__(6);
	var moment = __webpack_require__(27);

	module.exports = app.prototype.DualCollection = IDBCollection.extend({
	  keyPath: 'local_id',
	  mergeKeyPath: 'id',
	  _syncDelayed: true,

	  /**
	   * Items for download will be placed in queue
	   * Delay is the pause between the next items in queue
	   */
	  queue: [],
	  delay: 500, // server breathing spacing, can also be set via radio

	  url: function(){
	    var wc_api = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'wc_api'
	    });
	    return wc_api + this.name;
	  },

	  state: {
	    pageSize: 10
	  },

	  /**
	   *
	   */
	  fetch: function(options){
	    options = options || {};
	    if(options.remote){
	      return this.remoteFetch(options);
	    }
	    return IDBCollection.prototype.fetch.call(this, options);
	  },

	  /**
	   *
	   */
	  remoteFetch: function(options){
	    var self = this;
	    return this.sync('read', this, options)
	      .then(function(resp){
	        var models = self.parse(resp);
	        return IDBCollection.prototype.merge.call(self, models);
	      })
	      .then(function(models){
	        var ids = _.map(models, function(model){
	          return model.get('id');
	        });
	        self.dequeue(ids);
	      });
	  },

	  /**
	   * Full sync
	   * - Get any updated records
	   * - Audit using full list of remote ids vs local
	   * - Upload any local changes
	   */
	  fullSync: function(){
	    var self = this;

	    if(this._syncing){
	      debug('sync already in progress');
	      return;
	    }

	    this._syncing = true;
	    debug('fullSync started');
	    this.trigger('start:fullSync');

	    return this.fetchUpdated()
	      .then(function(){
	        return self.auditRecords();
	      })
	      .then(function(){
	        if(self._syncDelayed){
	          return self.syncDelayed();
	        }
	      })
	      .done(function(){ debug('fullSync complete'); })
	      .fail(function(err){ debug('fullSync failed', err); })
	      .always(function(){
	        self._syncing = false;
	        self.trigger('end:fullSync');
	      });

	  },

	  /**
	   * Fetch updated
	   * - if collection is empty, fetch the first page
	   * - else, get the latest updated_at from local collection
	   * - check server for any new updates
	   */
	  fetchUpdated: function(){
	    var self = this;

	    // no local records, possibly first fetch
	    if(this.length === 0){
	      return this.fetch({ remote: true });
	    }

	    //var last_update = this.formatDate( this.getState('last_update') );
	    var last_update = _.compact( this.pluck('updated_at') ).sort().pop();

	    //
	    return this.getRemoteIds(last_update)
	      .then(function(ids){
	        self.enqueue(ids);
	        return self.processQueue({
	          queue: ids,
	          all  : true
	        });
	      });

	  },

	  /**
	   * get delayed models and remote create/update
	   */
	  syncDelayed: function(){
	    var models = this.getDelayedModels();
	    var sync = _.map(models, function(model){
	      return model.remoteSync(null, model);
	    });
	    return $.when.apply(this, sync);
	  },

	  /**
	   * returns array of all delayed records
	   */
	  getDelayedModels: function() {
	    return this.filter(function(model){
	      return model.isDelayed();
	    });
	  },

	  /**
	   * Audit records
	   * - get full list of remote ids
	   * - compare to local ids
	   * - queue records for remote fetch
	   * - remove any garbage records
	   */
	  auditRecords: function(){
	    var local = this.pluck('id'),
	        self = this;

	    return this.getRemoteIds()
	      .then(function(remote){
	        var add = _.chain(remote).difference(local).compact().value(),
	            remove = _.chain(local).difference(remote).compact().value();
	        self.enqueue(add);
	        self.dequeue(remove);
	        return self.removeGarbage(remove);
	      })
	      .done(function(){ debug('audit complete'); })
	      .fail(function(err){ debug('audit failed', err); });

	  },

	  /**
	   * Get array of all entity ids from the server
	   * - optionally get ids modified since last_update
	   * - uses ajax for performance
	   */
	  getRemoteIds: function(last_update){
	    var ajaxurl = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'ajaxurl'
	    });

	    if(last_update){
	      debug('getting updated ids from server since ' + last_update);
	    } else {
	      debug('getting all ids from server');
	    }

	    return $.getJSON( ajaxurl, {
	      action        : 'wc_pos_get_all_ids',
	      type          : this.name,
	      updated_at_min: last_update
	    });
	  },

	  /**
	   * Remove garbage records, ie: records deleted on server
	   */
	  removeGarbage: function(ids){
	    var models = this.getModelsByRemoteIds(ids);

	    if(models.length === 0){
	      return;
	    }

	    this.remove(models);
	    return this.db.removeBatch(_.pluck(models, 'id'));
	  },

	  /**
	   * Turn array of remoteIds into array of models
	   * idAttribute = 'local_id'
	   * remoteIdAttribute = 'id
	   */
	  getModelsByRemoteIds: function(ids){
	    return this.filter(function(model){
	      return _(ids).contains(model.get('id'));
	    });
	  },

	  /**
	   * Add ids to queue for potential download
	   */
	  enqueue: function(ids){
	    if(!_.isArray(ids)){
	      return this.queue.push(ids);
	    }
	    this.queue = _.union(this.queue, ids);
	  },

	  /**
	   * Remove ids from queue
	   */
	  dequeue: function(ids){
	    if(!_.isArray(ids)){
	      this.queue = _.without(this.queue, ids);
	    } else {
	      this.queue = _.difference(this.queue, ids);
	    }
	  },

	  /**
	   *
	   */
	  hasAllRecords: function(){
	    return (this.queue.length === 0);
	  },

	  /**
	   * Process queue
	   * - take slice of ids from queue and remote fetch
	   * - optionally keep processing queue until empty
	   */
	  /* jshint -W071, -W074 */
	  processQueue: function(options){
	    options = options || {};
	    var queue = options.queue || _.clone(this.queue);
	    if(queue.length === 0 || this._processingQueue){
	      return;
	    }
	    this._processingQueue = true;
	    this.trigger('start:processQueue');

	    var self = this,
	        deferred = new $.Deferred(),
	        ids = queue.splice(0, this.state.pageSize).join(',');

	    this.fetch({
	      remote: true,
	      data: {
	        filter: options.filter || {
	          limit: -1,
	          'in': ids
	        }
	      }
	    })
	    .done(function(){
	      if(!options.all || queue.length === 0){
	        deferred.resolve();
	      } else {
	        deferred.progress(ids);
	        _.delay(self.processQueue.bind(self), self.getDelay(), options);
	      }
	    })
	    .fail(deferred.reject)
	    .always(function(){
	      self._processingQueue = false;
	      self.trigger('end:processQueue');
	    });

	    return deferred.promise();
	  },
	  /* jshint +W071, +W074 */

	  /**
	   * Allows delay to be added between process queue
	   * - may be necessary to ease server load
	   */
	  getDelay: function(){
	    return Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'delay'
	    }) || this.delay;
	  },

	  /**
	   * Clears the IDB storage and resets the collection
	   */
	  clear: function(){
	    if(!this.db){
	      return;
	    }

	    var self = this;
	    return IDBCollection.prototype.clear.call(this)
	      .then(function(){
	        self.queue = [];
	      });
	  },

	  /*
	   * Helper function to format Date.now() to RFC3339
	   * - returns 2015-03-11T02:30:43.925Z (with milliseconds)
	   * - undefined if no timestamp
	   */
	  formatDate: function(timestamp) {
	    if(timestamp){
	      //return moment(timestamp).utc().format('YYYY-MM-DDTHH:mm:ss') + 'Z';
	      return moment(timestamp).toISOString();
	    }
	  }

	});

/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * TODO: merge sync/idb.js & sync/idbsync.js?
	 */

	var Collection = __webpack_require__(24);
	//var debug = require('debug')('idbCollection');
	var app = __webpack_require__(2);
	var IndexedDB = __webpack_require__(25);
	var Radio = __webpack_require__(4);

	module.exports = app.prototype.IndexedDBCollection = Collection.extend({
	  name          : 'store',
	  storePrefix   : 'wc_pos_',
	  dbVersion     : 4005,
	  keyPath       : 'local_id',
	  autoIncrement : true,
	  indexes       : [
	    {name: 'local_id', keyPath: 'local_id', unique: true},
	    {name: 'id', keyPath: 'id', unique: true},
	    {name: 'status', keyPath: 'status', unique: false}
	  ],

	  constructor: function() {
	    Collection.apply(this, arguments);

	    var options = {
	      storeName     : this.name,
	      storePrefix   : this.storePrefix,
	      dbVersion     : this.dbVersion,
	      keyPath       : this.keyPath,
	      autoIncrement : this.autoIncrement,
	      indexes       : this.indexes,
	      defaultErrorHandler : function(error){
	        Radio.trigger('global', 'error', {
	          status: error.target.error.name,
	          message: error.target.error.message
	        });
	      }
	    };

	    this.db = new IndexedDB(options, this);
	    this.versionCheck();
	    this.db.open()
	      // error opening db
	      .fail(function(error){
	        Radio.trigger('global', 'error', {
	          status    : 'indexedDB error',
	          message   : error
	        });
	      });
	  },

	  merge: function(models){
	    var self = this;
	    return this.db.merge(models)
	      .then(function(){
	        var models = Array.prototype.slice.apply(arguments);
	        return self.add(models, {merge: true});
	      });
	  },

	  /**
	   * Clears the IDB storage and resets the collection
	   */
	  clear: function(){
	    if(!this.db){
	      return;
	    }

	    var self = this;
	    return this.db.open()
	      .then(function(){
	        self.reset();
	        return self.db.clear();
	      });
	  },

	  /**
	   * Each website will have a unique idbVersion number
	   * the version number is incremented on plugin update and some user actions
	   * this version check will compare the version numbers
	   * idb is flushed on version change
	   */
	  versionCheck: function(){
	    var name = this.name;

	    var newVersion = parseInt( Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'idbVersion'
	    }), 10 ) || 0;
	    var oldVersion = parseInt( Radio.request('entities', 'get', {
	      type: 'localStorage',
	      name: name + '_idbVersion'
	    }), 10 ) || 0;

	    if( newVersion !== oldVersion ){
	      this.clear().then(function(){
	        Radio.request('entities', 'set', {
	          type : 'localStorage',
	          name : name + '_idbVersion',
	          data : newVersion
	        });
	      });
	    }
	  }

	});

/***/ },
/* 24 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(16);
	var app = __webpack_require__(2);

	module.exports = app.prototype.Collection = bb.Collection.extend({
	  constructor: function() {
	    bb.Collection.apply(this, arguments);
	    this._isNew = true;
	    this.once('sync', function() {
	      this._isNew = false;
	    });
	  },

	  isNew: function() {
	    return this._isNew;
	  },

	  parse: function (resp){
	    return resp && resp[this.name] ? resp[this.name] : resp ;
	  },

	  sync: function(){
	    return bb.sync.apply(this, arguments);
	  }

	});

/***/ },
/* 25 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Backbone adapter for idb-wrapper api
	 */
	var IDBStore = __webpack_require__(26);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var bb = __webpack_require__(16);
	var noop = function (){};
	var defaultErrorHandler = function (error) {
	  throw error;
	};

	function IndexedDB(options, parent) {
	  this.parent = parent;
	  this.options = options;
	  if(options.defaultErrorHandler){
	    defaultErrorHandler = options.defaultErrorHandler;
	  }
	}

	var methods = {

	  /**
	   *
	   */
	  open: function () {
	    if(this._open){
	      return this._open;
	    }
	    var deferred = new $.Deferred(),
	        options = this.options || {};

	    options.onStoreReady = deferred.resolve;
	    options.onError = deferred.reject;

	    this.store = new IDBStore(options);

	    this._open = deferred.promise();
	    return this._open;
	  },

	  /**
	   * Add a new model to the store
	   */
	  create: function(model, options) {
	    options = options || {};
	    var onSuccess = options.success || noop,
	        onError = options.error || defaultErrorHandler,
	        data = this._returnAttributes(model),
	        keyPath = this.store.keyPath;

	    return this.put(data)
	      .then(function(insertedId){
	        data[keyPath] = insertedId;
	        onSuccess(data);
	        return data;
	      })
	      .fail(onError);
	  },

	  /**
	   * Update a model in the store
	   */
	  update: function(model, options) {
	    options = options || {};
	    var onSuccess = options.success || noop,
	        onError = options.error || defaultErrorHandler,
	        data = this._returnAttributes(model),
	        self = this;

	    return this.put(data)
	      .then(function(insertedId){
	        return self.get(insertedId)
	          .done(onSuccess)
	          .fail(onError);
	      })
	      .fail(onError);
	  },

	  /**
	   * Retrieve a model from the store
	   */
	  read: function(model, options) {
	    options = options || {};
	    var onSuccess = options.success || noop,
	        onError = options.error || defaultErrorHandler;
	    return this.get(model.id)
	      .done(onSuccess)
	      .fail(onError);
	  },

	  /**
	   * Delete a model from the store
	   */
	  destroy: function(model, options) {
	    if (model.isNew()) {
	      return false;
	    }
	    options = options || {};
	    var onSuccess = options.success || noop,
	        onError = options.error || defaultErrorHandler;

	    return this.remove(model.id)
	      .done(onSuccess)
	      .fail(onError);
	  },

	  /**
	   *
	   */
	  put: function (key, value) {
	    var deferred = new $.Deferred();

	    if (this.store.keyPath !== null) {
	      // in-line keys: one arg only (key == value)
	      this.store.put(key, deferred.resolve, deferred.reject);
	    } else {
	      // out-of-line keys: two args
	      this.store.put(key, value, deferred.resolve, deferred.reject);
	    }

	    return deferred.promise();
	  },

	  /**
	   *
	   */
	  get: function (key) {
	    var deferred = new $.Deferred();
	    this.store.get(key, deferred.resolve, deferred.reject);
	    return deferred.promise();
	  },

	  /**
	   *
	   */
	  remove: function(key){
	    if( _.isObject(key) && key.hasOwnProperty(this.store.keyPath) ) {
	      key = key[this.store.keyPath];
	    }
	    return this._remove(key);
	  },

	  _remove: function (key) {
	    var deferred = new $.Deferred();
	    this.store.remove(key, deferred.resolve, deferred.reject);
	    return deferred.promise();
	  },

	  /**
	   * Retrieve a collection from the store
	   */
	  getAll: function(options) {
	    var deferred = new $.Deferred();

	    var onSuccess = function (result) {
	      options.success.apply(this, arguments);
	      deferred.resolve(result);
	    };

	    var onError = function (result) {
	      options.error.apply(this, arguments);
	      deferred.reject(result);
	    };

	    this.store.getAll(onSuccess, onError);

	    return deferred.promise();
	  },

	  /**
	   * Iterates over the store using the given options and calling onItem
	   * for each entry matching the options.
	   */
	  iterate: function(options) {
	    options = options || {};
	    var deferred = new $.Deferred();
	    options.onEnd = deferred.resolve;
	    options.onError = deferred.reject;
	    var onItem = deferred.notify;
	    this.store.iterate(onItem, options);
	    return deferred.promise();
	  },

	  /**
	   * Creates a key range using specified options. This key range can be
	   * handed over to the count() and iterate() methods.
	   *
	   * Note: You must provide at least one or both of "lower" or "upper" value.
	   */
	  makeKeyRange: function(options) {
	    return this.store.makeKeyRange(options);
	  },

	  /**
	   * Perform a batch operation to save all models in the current collection to
	   * indexedDB.
	   */
	  saveAll: function() {
	    return this.putBatch(this.parent.toJSON());
	  },

	  /**
	   * Perform a batch operation to save and/or remove models in the current
	   * collection to indexedDB. This is a proxy to the idbstore `batch` method
	   */
	  batch: function(dataArray) {
	    var deferred = new $.Deferred();
	    this.store.batch(dataArray, deferred.resolve, deferred.reject);
	    return deferred.promise();
	  },

	  /**
	   * Perform a batch put operation to save models to indexedDB. This is a
	   * proxy to the idbstore `putBatch` method
	   */
	  putBatch: function(dataArray) {
	    if( !_.isArray(dataArray) ){
	      return this.put(dataArray);
	    }
	    return this._putBatch(dataArray);
	  },

	  _putBatch: function(dataArray) {
	    var deferred = new $.Deferred();
	    this.store.putBatch(dataArray, deferred.resolve, deferred.reject);
	    return deferred.promise();
	  },

	  /**
	   * Perform a batch operation to remove models from indexedDB. This is a
	   * proxy to the idbstore `removeBatch` method
	   */
	  removeBatch: function(keyArray) {
	    if( !_.isArray(keyArray) ){
	      return this.remove(keyArray);
	    }
	    return this._removeBatch(keyArray);
	  },

	  _removeBatch: function(keyArray){
	    var deferred = new $.Deferred();
	    this.store.removeBatch(keyArray, deferred.resolve, deferred.reject);
	    return deferred.promise();
	  },

	  /**
	   * Clears all content from the current indexedDB for this collection/model
	   */
	  clear: function() {
	    var deferred = new $.Deferred();
	    this.store.clear(deferred.resolve, deferred.reject);
	    return deferred.promise();
	  },

	  /**
	   *
	   */
	  query: function(index, keyRange){
	    var deferred = new $.Deferred();

	    this.store.query(deferred.resolve, {
	      index: index,
	      keyRange: keyRange,
	      onError: deferred.reject
	    });

	    return deferred.promise();
	  },

	  /**
	   * select records by {key: value}
	   */
	  getByAttribute: function(attribute){
	    var index = _.chain(attribute).keys().first().value();
	    var keyRange = this.store.makeKeyRange({
	      only: _.chain(attribute).values().first().value()
	    });
	    return this.query(index, keyRange);
	  },

	  merge: function(models){
	    models = !_.isArray(models) ? [models] : models;
	    if(!this.parent.mergeKeyPath){
	      return this.putBatch(models);
	    }
	    var merge = _.map(models, this._merge, this);
	    return $.when.apply(this, merge);
	  },

	  _merge: function(model){
	    var mergeKeyPath = this.parent.mergeKeyPath,
	        keyPath = this.store.keyPath,
	        self = this,
	        opts = {};

	    if(model[keyPath]){
	      return this.update(model);
	    }

	    opts[mergeKeyPath] = model[mergeKeyPath];

	    return this.getByAttribute(opts)
	      .then(function(array){
	        var local = _.first(array);
	        if(local){
	          model[keyPath] = local[keyPath];
	          return self.update(model);
	        }
	        return self.create(model);
	      });
	  },

	  _returnAttributes: function(model){
	    if(model instanceof bb.Model){
	      return model.toJSON();
	    }
	    return model;
	  }
	};

	_.extend(IndexedDB.prototype, methods);
	module.exports = IndexedDB;

/***/ },
/* 26 */
/***/ function(module, exports) {

	module.exports = IDBStore;

/***/ },
/* 27 */
/***/ function(module, exports) {

	module.exports = moment;

/***/ },
/* 28 */
/***/ function(module, exports, __webpack_require__) {

	var DualModel = __webpack_require__(29);
	var _ = __webpack_require__(5);
	var Variations = __webpack_require__(33);
	var FilteredCollection = __webpack_require__(35);

	module.exports = DualModel.extend({
	  name: 'product',

	  // this is an array of fields used by FilterCollection.matchmaker()
	  fields: ['title'],

	  // data types
	  schema: {
	    price         : 'number',
	    regular_price : 'number',
	    sale_price    : 'number',
	    stock_quantity: 'number'
	  },

	  initialize: function(){
	    this.on({
	      'change:updated_at': this.onUpdate
	    });
	  },

	  onUpdate: function(){
	    // update stock
	    if( this.get('type') === 'variable' ){
	      var variations = this.getVariations().superset();
	      variations.set( this.get('variations') );
	    }
	  },

	  /**
	   * Helper functions to display attributes vs variations
	   */
	  productAttributes: function(){
	    return _.chain(this.get('attributes'))
	      .where({variation: false})
	      .where({visible: true})
	      .value();
	  },

	  productVariations: function(){
	    return _.where(this.get('attributes'), {variation: true});
	  },

	  /**
	   * Special cases for product model filter
	   * @param {Array} tokens An array of query tokens, see QParser
	   * @param {Object} methods Helper match methods
	   * @param {Function} callback
	   */
	  matchMaker: function(tokens, methods, callback){

	    var match = _.all(tokens, function(token){

	      // barcode
	      if( token.type === 'prefix' && token.prefix === 'barcode' ){
	        if(token.query){ return this.barcodeMatch(token.query); }
	      }

	      // cat
	      if( token.type === 'prefix' && token.prefix === 'cat' ){
	        token.prefix = 'categories';
	        return methods.prefix(token, this);
	      }

	    }, this);

	    //if(match){
	    //  return match;
	    //}

	    // the original matchMaker
	    return match ? match : callback(tokens, this);

	  },

	  barcodeMatch: function(barcode){
	    var type = this.get('type'),
	        test = this.get('barcode').toLowerCase(),
	        value = barcode.toString().toLowerCase();

	    if(test === value) {
	      if(type !== 'variable'){
	        this.trigger('match:barcode', this);
	      }
	      return true;
	    }

	    if(type !== 'variable'){
	      return this.partialBarcodeMatch(test, value);
	    }

	    return this.variableBarcodeMatch(test, value);
	  },

	  partialBarcodeMatch: function(test, value){
	    if(test.indexOf( value ) !== -1) {
	      return true;
	    }
	    return false;
	  },

	  variableBarcodeMatch: function(test, value){
	    var match;

	    this.getVariations().superset().each(function(variation){
	      var vtest = variation.get('barcode').toLowerCase();
	      if(vtest === value){
	        match = variation;
	        return;
	      }
	      if(vtest.indexOf( value ) !== -1) {
	        match = 'partial';
	        return;
	      }
	    });

	    if(match){
	      if(match !== 'partial'){
	        this.trigger('match:barcode', match, this);
	      }
	      return true;
	    }

	    return this.partialBarcodeMatch(test, value);
	  },

	  /**
	   * Construct variable options from variation array
	   * - variable.attributes includes all options, including those not used
	   */
	  getVariationOptions: function(){
	    if( this._variationOptions ) {
	      return this._variationOptions;
	    }

	    var variations = this.get('variations');

	    // pluck all options, eg:
	    // { Color: ['Black', 'Blue'], Size: ['Small', 'Large'] }
	    var result = _.pluck(variations, 'attributes')
	      .reduce(function(result, attrs){
	        _.each(attrs, function(attr){
	          if(result[attr.name]){
	            return result[attr.name].push(attr.option);
	          }
	          result[attr.name] = [attr.option];
	        });
	        return result;
	      }, {});

	    // map options with consistent keys
	    this._variationOptions = _.map(result, function(options, name){
	      return {
	        'name': name,
	        'options': _.uniq( options )
	      };
	    });

	    return this._variationOptions;
	  },

	  /**
	   *
	   */
	  getVariations: function(){
	    if( this.get('type') !== 'variable' ){ return false; }
	    if( ! this._variations ){
	      var variations = new Variations(this.get('variations'), { parent: this });
	      this._variations = new FilteredCollection(variations);
	    }
	    return this._variations;
	  }

	});

/***/ },
/* 29 */
/***/ function(module, exports, __webpack_require__) {

	var DeepModel = __webpack_require__(30);
	var app = __webpack_require__(2);
	var _ = __webpack_require__(5);
	var debug = __webpack_require__(13)('dualModel');

	module.exports = app.prototype.DualModel = DeepModel.extend({
	  idAttribute: 'local_id',
	  remoteIdAttribute: 'id',
	  fields: ['title'],

	  validate: function(attrs){
	    var obj = {};
	    if(attrs[this.idAttribute]) {
	      obj[this.idAttribute] = parseInt(attrs[this.idAttribute], 10);
	    }
	    if(attrs[this.remoteIdAttribute]){
	      obj[this.remoteIdAttribute] = parseInt(attrs[this.remoteIdAttribute], 10);
	    }
	    this.set(obj, {silent: true});
	  },

	  url: function(){
	    var remoteId = this.get(this.remoteIdAttribute),
	        urlRoot = _.result(this.collection, 'url');

	    if(remoteId){
	      return '' + urlRoot + '/' + remoteId + '/';
	    }
	    return urlRoot;
	  },

	  // delayed states
	  states: {
	    //'patch'  : 'UPDATE_FAILED',
	    'update' : 'UPDATE_FAILED',
	    'create' : 'CREATE_FAILED',
	    'delete' : 'DELETE_FAILED'
	  },

	  hasRemoteId: function() {
	    return !!this.get(this.remoteIdAttribute);
	  },

	  isDelayed: function() {
	    var status = this.get('status');
	    return status === this.states['update'] ||
	           status === this.states['create'] ||
	           status === this.states['delete'];
	  },

	  /**
	   * - sync to idb with correct status
	   * - if remote, sync to remote
	   */
	  sync: function(method, model, options){
	    options = options || {};
	    var opts = _.clone(options);
	    opts.remote = undefined;
	    var m = method === 'patch' ? 'update' : method;

	    this.setStatus(m);

	    return DeepModel.prototype.sync.call(this, m, model, opts)
	      .then(function(){
	        if(options.remote){
	          return model.remoteSync(method, model, options);
	        }
	      });
	  },

	  remoteSync: function(method, model, options){
	    model = model || this;
	    options = options || {};
	    options.remote = true;
	    method = method || model.getMethod();

	    return DeepModel.prototype.sync.call(this, method, model, options)
	      .then(function(resp){
	        if(resp){
	          var data = model.parse(resp);
	          return model.merge(data);
	        }
	      });
	  },

	  setStatus: function(method){
	    if(this.states[method]){
	      if(method === 'update' && !this.hasRemoteId()){
	        method = 'create';
	      }
	      this.set({ status: this.states[method] });
	    }
	  },

	  getMethod: function(){
	    var status = this.get('status');
	    var remoteMethod = _.findKey(this.states, function(state) {
	      return state === status;
	    });
	    if(remoteMethod){
	      return remoteMethod;
	    } else {
	      debug('No method given for remote sync');
	    }
	  },

	  merge: function(resp){
	    // todo: merge
	    // - merge should take bb & json?
	    this.set(resp);
	    if(this.isDelayed()){
	      this.unset('status');
	    }
	    if(this.collection && this.collection.db){
	      return this.collection.merge( this.toJSON() );
	    }
	  }

	});

/***/ },
/* 30 */
/***/ function(module, exports, __webpack_require__) {

	var app = __webpack_require__(2);
	var Model = __webpack_require__(31);
	var DeepModel = __webpack_require__(32);

	module.exports = app.prototype.DeepModel = Model.extend(DeepModel);

/***/ },
/* 31 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(16);
	var app = __webpack_require__(2);
	var _ = __webpack_require__(5);
	//var Radio = require('backbone.radio');

	// parsing functions
	var parse = {
	  'float': parseFloat,
	  'int': parseInt,
	  'number': function(num){
	    num = Number(num);
	    return _.isNaN(num) ? 0 : num;
	  }
	};

	module.exports = app.prototype.Model = bb.Model.extend({

	  parse: function (resp){
	    var data = resp && resp[this.name] ? resp[this.name]  : resp;
	    if( ! data ){
	      return;
	    }

	    // check data type
	    _.each( this.schema, function( val, attr ) {

	      // if attribute exists
	      if( ! _.has( data, attr ) ){
	        return;
	      }

	      // string, eg: 'float'
	      if( _.isString(val) && parse[val] ){
	        data[attr] = parse[val]( data[attr] );
	      }

	    }, this);

	    return data;
	  }

	});

/***/ },
/* 32 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);

	/**
	 * Takes a nested object and returns a shallow object keyed with the path names
	 * e.g. { "level1.level2": "value" }
	 *
	 * @param  {Object}      Nested object e.g. { level1: { level2: 'value' } }
	 * @return {Object}      Shallow object with path names e.g. { 'level1.level2': 'value' }
	 */
	function objToPaths(obj) {
		var ret = {},
			separator = DeepModel.keyPathSeparator;

		for (var key in obj) {
			var val = obj[key];

			if (val && (val.constructor === Object || val.constructor === Array) && !_.isEmpty(val)) {
				//Recursion for embedded objects
				var obj2 = objToPaths(val);

				for (var key2 in obj2) {
					var val2 = obj2[key2];

					ret[key + separator + key2] = val2;
				}
			} else {
				ret[key] = val;
			}
		}

		return ret;
	}

	/**
	 * [getNested description]
	 * @param  {object} obj           to fetch attribute from
	 * @param  {string} path          path e.g. 'user.name'
	 * @param  {[type]} return_exists [description]
	 * @return {mixed}                [description]
	 */
	function getNested(obj, path, return_exists) {
		var separator = DeepModel.keyPathSeparator;

		var fields = path ? path.split(separator) : [];
		var result = obj;
		return_exists || (return_exists === false);
		for (var i = 0, n = fields.length; i < n; i++) {
			if (return_exists && !_.has(result, fields[i])) {
				return false;
			}
			result = result[fields[i]];

			if (result == null && i < n - 1) {
				result = {};
			}

			if (typeof result === 'undefined') {
				if (return_exists) {
					return true;
				}
				return result;
			}
		}
		if (return_exists) {
			return true;
		}
		return result;
	}



	/**
	 * @param {Object} obj                Object to fetch attribute from
	 * @param {String} path               Object path e.g. 'user.name'
	 * @param {Object} [options]          Options
	 * @param {Boolean} [options.unset]   Whether to delete the value
	 * @param {Mixed}                     Value to set
	 */
	function setNested(obj, path, val, options) {
		options = options || {};

		var separator = DeepModel.keyPathSeparator;

		var fields = path ? path.split(separator) : [];
		var result = obj;
		for (var i = 0, n = fields.length; i < n && result !== undefined; i++) {
			var field = fields[i];

			//If the last in the path, set the value
			if (i === n - 1) {
				options.unset ? delete result[field] : result[field] = val;
			} else {
				//Create the child object if it doesn't exist, or isn't an object
				if (typeof result[field] === 'undefined' || !_.isObject(result[field])) {
					// If trying to remove a field that doesn't exist, then there's no need
					// to create its missing parent (doing so causes a problem with
					// hasChanged()).
					if (options.unset) {
						delete result[field]; // in case parent exists but is not an object
						return;
					}
					var nextField = fields[i + 1];

					// create array if next field is integer, else create object
					result[field] = /^\d+$/.test(nextField) ? [] : {};
				}

				//Move onto the next part of the path
				result = result[field];
			}
		}
	}

	function deleteNested(obj, path) {
		setNested(obj, path, null, {
			unset: true
		});
	}

	var DeepModel = {

		// Override constructor
		// Support having nested defaults by using _.deepExtend instead of _.extend
		constructor: function(attributes, options) {
			var attrs = attributes || {};
			this.cid = _.uniqueId('c');
			this.attributes = {};
			if (options && options.collection) this.collection = options.collection;
			if (options && options.parse) attrs = this.parse(attrs, options) || {};
	    attrs = _.merge({}, _.result(this, 'defaults'), attrs);
			this.set(attrs, options);
			this.changed = {};
			this.initialize.apply(this, arguments);
		},

		// Return a copy of the model's `attributes` object.
		toJSON: function(options) {
			return _.merge({}, this.attributes);
		},

		// Override get
		// Supports nested attributes via the syntax 'obj.attr' e.g. 'author.user.name'
		get: function(attr) {
			return getNested(this.attributes, attr);
		},

		// Override set
		// Supports nested attributes via the syntax 'obj.attr' e.g. 'author.user.name'
		set: function(key, val, options) {
			var attr, attrs, unset, changes, silent, changing, prev, current;
			if (key == null) return this;

			// Handle both `"key", value` and `{key: value}` -style arguments.
			if (typeof key === 'object') {
				attrs = key;
				options = val || {};
			} else {
				(attrs = {})[key] = val;
			}

			options || (options = {});

			// Run validation.
			if (!this._validate(attrs, options)) return false;

			// Extract attributes and options.
			unset = options.unset;
			silent = options.silent;
			changes = [];
			changing = this._changing;
			this._changing = true;

			if (!changing) {
				this._previousAttributes = _.merge({}, this.attributes); //<custom>: Replaced _.clone with _.deepClone
				this.changed = {};
			}
			current = this.attributes, prev = this._previousAttributes;

			// Check for changes of `id`.
			if (this.idAttribute in attrs) this.id = attrs[this.idAttribute];

			//<custom code>
			attrs = objToPaths(attrs);
			//</custom code>

			// For each `set` attribute, update or delete the current value.
			for (attr in attrs) {
				val = attrs[attr];

				//<custom code>: Using getNested, setNested and deleteNested
				if (!_.isEqual(getNested(current, attr), val)) changes.push(attr);
				if (!_.isEqual(getNested(prev, attr), val)) {
					setNested(this.changed, attr, val);
				} else {
					deleteNested(this.changed, attr);
				}
				unset ? deleteNested(current, attr) : setNested(current, attr, val);
				//</custom code>
			}

			// Trigger all relevant attribute changes.
			if (!silent) {
				if (changes.length) this._pending = true;

				//<custom code>
				var separator = DeepModel.keyPathSeparator;
				var alreadyTriggered = {}; // * @restorer

				for (var i = 0, l = changes.length; i < l; i++) {
					var key = changes[i];

					if (!alreadyTriggered.hasOwnProperty(key) || !alreadyTriggered[key]) { // * @restorer
						alreadyTriggered[key] = true; // * @restorer
						this.trigger('change:' + key, this, getNested(current, key), options);
					} // * @restorer

					var fields = key.split(separator);

					//Trigger change events for parent keys with wildcard (*) notation
					for (var n = fields.length - 1; n > 0; n--) {
						var parentKey = fields.slice(0, n).join(separator),
							wildcardKey = parentKey + separator + '*';

						if (!alreadyTriggered.hasOwnProperty(wildcardKey) || !alreadyTriggered[wildcardKey]) { // * @restorer
							alreadyTriggered[wildcardKey] = true; // * @restorer
							this.trigger('change:' + wildcardKey, this, getNested(current, parentKey), options);
						} // * @restorer

						// + @restorer
						if (!alreadyTriggered.hasOwnProperty(parentKey) || !alreadyTriggered[parentKey]) {
							alreadyTriggered[parentKey] = true;
							this.trigger('change:' + parentKey, this, getNested(current, parentKey), options);
						}
						// - @restorer
					}
					//</custom code>
				}
			}

			if (changing) return this;
			if (!silent) {
				while (this._pending) {
					this._pending = false;
					this.trigger('change', this, options);
				}
			}
			this._pending = false;
			this._changing = false;
			return this;
		},

		// Clear all attributes on the model, firing `"change"` unless you choose
		// to silence it.
		clear: function(options) {
			var attrs = {};
			var shallowAttributes = objToPaths(this.attributes);
			for (var key in shallowAttributes) attrs[key] = void 0;
			return this.set(attrs, _.extend({}, options, {
				unset: true
			}));
		},

		// Determine if the model has changed since the last `"change"` event.
		// If you specify an attribute name, determine if that attribute has changed.
		hasChanged: function(attr) {
			if (attr == null) {
				return !_.isEmpty(this.changed);
			}
			return getNested(this.changed, attr) !== undefined;
		},

		// Return an object containing all the attributes that have changed, or
		// false if there are no changed attributes. Useful for determining what
		// parts of a view need to be updated and/or what attributes need to be
		// persisted to the server. Unset attributes will be set to undefined.
		// You can also pass an attributes object to diff against the model,
		// determining if there *would be* a change.
		changedAttributes: function(diff) {
			//<custom code>: objToPaths
			if (!diff) return this.hasChanged() ? objToPaths(this.changed) : false;
			//</custom code>

			var old = this._changing ? this._previousAttributes : this.attributes;

			//<custom code>
			diff = objToPaths(diff);
			old = objToPaths(old);
			//</custom code>

			var val, changed = false;
			for (var attr in diff) {
				if (_.isEqual(old[attr], (val = diff[attr]))) continue;
				(changed || (changed = {}))[attr] = val;
			}
			return changed;
		},

		// Get the previous value of an attribute, recorded at the time the last
		// `"change"` event was fired.
		previous: function(attr) {
			if (attr == null || !this._previousAttributes) {
				return null;
			}
			//<custom code>
			return getNested(this._previousAttributes, attr);
			//</custom code>
		},

		// Get all of the attributes of the model at the time of the previous
		// `"change"` event.
		previousAttributes: function() {
			return _.merge({}, this._previousAttributes);
		}
	};

	//Config; override in your app to customise
	DeepModel.keyPathSeparator = '.';


	module.exports = DeepModel;

/***/ },
/* 33 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(24);
	var Model = __webpack_require__(34);
	var Radio = __webpack_require__(4);
	var _ = __webpack_require__(5);

	module.exports = Collection.extend({
	  model: Model,

	  url: function(){
	    var wc_api = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'wc_api'
	    });
	    return wc_api + 'products';
	  },

	  initialize: function() {
	    this._isNew = false;
	  },

	  /**
	   * same as _.compact
	   * except allows 0
	   */
	  /* jshint -W074 */
	  compact: function(array) {
	    var index = -1,
	      length = array ? array.length : 0,
	      resIndex = -1,
	      result = [];

	    while (++index < length) {
	      var value = array[index];
	      if (value === 0 || value) {
	        result[++resIndex] = value;
	      }
	    }
	    return result;
	  },
	  /* jshint +W074 */

	  range: function(attr){
	    var attrs = this.compact( this.pluck(attr)), min = 0, max = 0;
	    if( !_.isEmpty(attrs) ) {
	      min = _(attrs).min();
	      max = _(attrs).max();
	    }
	    return _.uniq([min, max]);
	  }

	});

/***/ },
/* 34 */
/***/ function(module, exports, __webpack_require__) {

	//var Model = require('lib/config/model');
	var Model = __webpack_require__(30);

	module.exports = Model.extend({
	  name: 'product',
	  defaults: {
	    type: 'variation'
	  },

	  // data types
	  schema: {
	    price         : 'number',
	    regular_price : 'number',
	    sale_price    : 'number',
	    stock_quantity: 'number'
	  },

	  initialize: function(attributes, options){
	    options = options || {};
	    this.parent = options.parent;
	    this.set({ title: options.parent.get('title') });
	  },

	  // copy variation to parent
	  save: function(attributes, options){
	    var self = this;
	    return Model.prototype.save.call(this, attributes, options)
	      .then(function(){
	        self.parent.set({ variations: self.collection.toJSON() });
	        self.parent.merge();
	      });
	  }

	});

/***/ },
/* 35 */
/***/ function(module, exports, __webpack_require__) {

	/* jshint -W071, -W003, -W021 */
	var _ = __webpack_require__(5);
	var Backbone = __webpack_require__(16);
	var FilteredCollection = __webpack_require__(36);
	var SortedCollection = __webpack_require__(39);
	var PaginatedCollection = __webpack_require__(41);
	var proxyCollection = __webpack_require__(37);
	var proxyEvents = __webpack_require__(42);
	var query = __webpack_require__(43);

	// extend FilteredCollection with query methods
	_.extend(FilteredCollection.prototype, query);

	function Obscura(superset, options) {
	  this._superset = superset;
	  this._filtered = new FilteredCollection(superset, options);
	  this._sorted = new SortedCollection(this._filtered, options);
	  this._paginated = new PaginatedCollection(this._sorted, options);
	  proxyCollection(this._paginated, this);
	  proxyEvents.call(this, this._filtered, filteredEvents);
	  proxyEvents.call(this, this._sorted, sortedEvents);
	  proxyEvents.call(this, this._paginated, paginatedEvents);
	  this.initialize(options);
	}

	var methods = {
	  superset: function () {
	    return this._superset;
	  },
	  getFilteredLength: function () {
	    return this._filtered.length;
	  },
	  removeTransforms: function () {
	    this._filtered.resetFilters();
	    this._sorted.removeSort();
	    this._paginated.removePagination();
	    return this;
	  },
	  destroy: function () {
	    this.stopListening();
	    this._filtered.destroy();
	    this._sorted.destroy();
	    this._paginated.destroy();
	    this.length = 0;
	    this.trigger('obscura:destroy');
	  }
	};

	var filteredMethods = [
	  'filterBy',
	  'removeFilter',
	  'resetFilters',
	  'refilter',
	  'hasFilter',
	  'getFilters',
	  'query',
	  'getQuery',
	  'getTokens',
	  'getRemoteFilter'
	];
	var filteredEvents = [
	  'filtered:add',
	  'filtered:remove',
	  'filtered:reset'
	];
	var sortedMethods = [
	  'setSort',
	  'reverseSort',
	  'removeSort'
	];
	var sortedEvents = [
	  'sorted:add',
	  'sorted:remove'
	];
	var paginatedMethods = [
	  'setPerPage',
	  'setPage',
	  'getPerPage',
	  'getNumPages',
	  'getPage',
	  'hasNextPage',
	  'hasPrevPage',
	  'nextPage',
	  'prevPage',
	  'movePage',
	  'removePagination',
	  'firstPage',
	  'lastPage',
	  'appendNextPage'
	];
	var paginatedEvents = [
	  'paginated:change:perPage',
	  'paginated:change:page',
	  'paginated:change:numPages'
	];
	var unsupportedMethods = [
	  'add',
	  'create',
	  'remove',
	  'set',
	  'reset',
	  'sort',
	  'parse',
	  'sync',
	  'fetch',
	  'push',
	  'pop',
	  'shift',
	  'unshift'
	];

	_.each(filteredMethods, function (method) {
	  methods[method] = function () {
	    var result = FilteredCollection.prototype[method]
	      .apply(this._filtered, arguments);
	    return result === this._filtered ? this : result;
	  };
	});
	_.each(paginatedMethods, function (method) {
	  methods[method] = function () {
	    var result = PaginatedCollection.prototype[method]
	      .apply(this._paginated, arguments);
	    return result === this._paginated ? this : result;
	  };
	});
	_.each(sortedMethods, function (method) {
	  methods[method] = function () {
	    var result = SortedCollection.prototype[method]
	      .apply(this._sorted, arguments);
	    return result === this._sorted ? this : result;
	  };
	});
	_.each(unsupportedMethods, function (method) {
	  methods[method] = function () {
	    throw new Error(
	      'Backbone.Obscura: Unsupported method: ' +
	      method + 'called on read-only proxy'
	    );
	  };
	});

	_.extend(Obscura.prototype, methods, Backbone.Events);
	Obscura = Backbone.Collection.extend(Obscura.prototype);
	Obscura.FilteredCollection = FilteredCollection;
	Obscura.SortedCollection = SortedCollection;
	Obscura.PaginatedCollection = PaginatedCollection;
	module.exports = Obscura;
	/* jshint +W071, +W003, +W021 */

/***/ },
/* 36 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);
	var Backbone = __webpack_require__(16);
	var proxyCollection = __webpack_require__(37);
	var createFilter = __webpack_require__(38);

	// Beware of `this`
	// All of the following functions are meant to be called in the context
	// of the FilteredCollection object, but are not public functions.

	function invalidateCache() {
	  this._filterResultCache = {};
	}

	function invalidateCacheForFilter(filterName) {
	  for (var cid in this._filterResultCache) {
	    if (this._filterResultCache.hasOwnProperty(cid)) {
	      delete this._filterResultCache[cid][filterName];
	    }
	  }
	}

	function addFilter(filterName, filterObj) {
	  // If we've already had a filter of this name, we need to invalidate
	  // any and all of the cached results
	  if (this._filters[filterName]) {
	    invalidateCacheForFilter.call(this, filterName);
	  }

	  this._filters[filterName] = filterObj;
	  this.trigger('filtered:add', filterName);
	}

	function removeFilter(filterName) {
	  delete this._filters[filterName];

	  /* todo: integrate query methods */
	  delete this._query;
	  delete this._tokens;

	  invalidateCacheForFilter.call(this, filterName);
	  this.trigger('filtered:remove', filterName);
	}

	function execFilterOnModel(model) {
	  if (!this._filterResultCache[model.cid]) {
	    this._filterResultCache[model.cid] = {};
	  }

	  var cache = this._filterResultCache[model.cid];

	  for (var filterName in this._filters) {
	    if (this._filters.hasOwnProperty(filterName)) {
	      // if we haven't already calculated this, calculate it and cache
	      if (!cache.hasOwnProperty(filterName)) {
	        cache[filterName] = this._filters[filterName].fn(model);
	      }
	      if (!cache[filterName]) {
	        return false;
	      }
	    }
	  }
	  return true;
	}

	function execFilter() {
	  var filtered = [];

	  // Filter the collection
	  if (this._superset) {
	    filtered = this._superset.filter(_.bind(execFilterOnModel, this));
	  }

	  this._collection.reset(filtered);
	  this.length = this._collection.length;
	}

	function onAddChange(model) {
	  // reset the cached results
	  this._filterResultCache[model.cid] = {};

	  if (execFilterOnModel.call(this, model)) {
	    if (!this._collection.get(model.cid)) {
	      var index = this.superset().indexOf(model);

	      // Find the index at which to insert the model in the
	      // filtered collection by finding the index of the
	      // previous non-filtered model in the filtered collection
	      var filteredIndex = null;
	      for (var i = index - 1; i >= 0; i -= 1) {
	        if (this.contains(this.superset().at(i))) {
	          filteredIndex = this.indexOf(this.superset().at(i)) + 1;
	          break;
	        }
	      }
	      filteredIndex = filteredIndex || 0;

	      this._collection.add(model, { at: filteredIndex });
	    }
	  } else {
	    if (this._collection.get(model.cid)) {
	      this._collection.remove(model);
	    }
	  }
	  this.length = this._collection.length;
	}

	// This fires on 'change:[attribute]' events. We only want to
	// remove this model if it fails the test, but not add it if
	// it does. If we remove it, it will prevent the 'change'
	// events from being forwarded, and if we add it, it will cause
	// an unneccesary 'change' event to be forwarded without the
	// 'change:[attribute]' that goes along with it.
	function onModelAttributeChange(model) {
	  // reset the cached results
	  this._filterResultCache[model.cid] = {};

	  if (!execFilterOnModel.call(this, model)) {
	    if (this._collection.get(model.cid)) {
	      this._collection.remove(model);
	    }
	  }
	}

	function onAll(eventName, model, value) {
	  if (eventName.slice(0, 7) === "change:") {
	    onModelAttributeChange.call(this, arguments[1]);
	  }
	}

	function onModelRemove(model) {
	  if (this.contains(model)) {
	    this._collection.remove(model);
	  }
	  this.length = this._collection.length;
	}

	function Filtered(superset) {
	  // Save a reference to the original collection
	  this._superset = superset;

	  // The idea is to keep an internal backbone collection with the filtered
	  // set, and expose limited functionality.
	  this._collection = new Backbone.Collection(superset.toArray());
	  proxyCollection(this._collection, this);

	  // Set up the filter data structures
	  this.resetFilters();

	  this.listenTo(this._superset, 'reset sort', execFilter);
	  this.listenTo(this._superset, 'add change', onAddChange);
	  this.listenTo(this._superset, 'remove', onModelRemove);
	  this.listenTo(this._superset, 'all', onAll);
	}

	var methods = {

	  defaultFilterName: '__default',

	  filterBy: function(filterName, filter) {
	    // Allow the user to skip the filter name if they're only using one filter
	    if (!filter) {
	      filter = filterName;
	      filterName = this.defaultFilterName;
	    }

	    addFilter.call(this, filterName, createFilter(filter));

	    execFilter.call(this);
	    return this;
	  },

	  removeFilter: function(filterName) {
	    if (!filterName) {
	      filterName = this.defaultFilterName;
	    }

	    removeFilter.call(this, filterName);

	    execFilter.call(this);
	    return this;
	  },

	  resetFilters: function() {
	    this._filters = {};
	    invalidateCache.call(this);

	    this.trigger('filtered:reset');

	    execFilter.call(this);
	    return this;
	  },

	  superset: function() {
	    return this._superset;
	  },

	  refilter: function(arg) {
	    if (typeof arg === "object" && arg.cid) {
	      // is backbone model, refilter that one
	      onAddChange.call(this, arg);
	    } else {
	      // refilter everything
	      invalidateCache.call(this);
	      execFilter.call(this);
	    }

	    return this;
	  },

	  getFilters: function() {
	    return  _.keys(this._filters);
	  },

	  hasFilter: function(name) {
	    return _.contains(this.getFilters(), name);
	  },

	  destroy: function() {
	    this.stopListening();
	    this._collection.reset([]);
	    this._superset = this._collection;
	    this.length = 0;

	    this.trigger('filtered:destroy');
	  }

	};

	// Build up the prototype
	_.extend(Filtered.prototype, methods, Backbone.Events);

	module.exports = Filtered;

/***/ },
/* 37 */
/***/ function(module, exports, __webpack_require__) {

	
	var _ = __webpack_require__(5);
	var Backbone = __webpack_require__(16);

	// Methods in the collection prototype that we won't expose
	var blacklistedMethods = [
	  "_onModelEvent", "_prepareModel", "_removeReference", "_reset", "add",
	  "initialize", "sync", "remove", "reset", "set", "push", "pop", "unshift",
	  "shift", "sort", "parse", "fetch", "create", "model", "off", "on",
	  "listenTo", "listenToOnce", "bind", "trigger", "once", "stopListening"
	];

	var eventWhiteList = [
	  'add', 'remove', 'reset', 'sort', 'destroy', 'sync', 'request', 'error'
	];

	function proxyCollection(from, target) {

	  function updateLength() {
	    target.length = from.length;
	  }

	  function pipeEvents(eventName) {
	    var args = _.toArray(arguments);
	    var isChangeEvent = eventName === 'change' ||
	                        eventName.slice(0, 7) === 'change:';

	    // In the case of a `reset` event, the Collection.models reference
	    // is updated to a new array, so we need to update our reference.
	    if (eventName === 'reset') {
	      target.models = from.models;
	    }

	    if (_.contains(eventWhiteList, eventName)) {
	      if (_.contains(['add', 'remove', 'destroy'], eventName)) {
	        args[2] = target;
	      } else if (_.contains(['reset', 'sort'], eventName)) {
	        args[1] = target;
	      }
	      target.trigger.apply(this, args);
	    } else if (isChangeEvent) {
	      // In some cases I was seeing change events fired after the model
	      // had already been removed from the collection.
	      if (target.contains(args[1])) {
	        target.trigger.apply(this, args);
	      }
	    }
	  }

	  var methods = {};

	  _.each(_.functions(Backbone.Collection.prototype), function(method) {
	    if (!_.contains(blacklistedMethods, method)) {
	      methods[method] = function() {
	        return from[method].apply(from, arguments);
	      };
	    }
	  });

	  _.extend(target, Backbone.Events, methods);

	  target.listenTo(from, 'all', updateLength);
	  target.listenTo(from, 'all', pipeEvents);
	  target.models = from.models;

	  updateLength();
	  return target;
	}

	module.exports = proxyCollection;



/***/ },
/* 38 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);

	// Converts a key and value into a function that accepts a model
	// and returns a boolean.
	function convertKeyValueToFunction(key, value) {
	  return function(model) {
	    return model.get(key) === value;
	  };
	}

	// Converts a key and an associated filter function into a function
	// that accepts a model and returns a boolean.
	function convertKeyFunctionToFunction(key, fn) {
	  return function(model) {
	    return fn(model.get(key));
	  };
	}

	function createFilterObject(filterFunction, keys) {
	  // Make sure the keys value is either an array or null
	  if (!_.isArray(keys)) {
	    keys = null;
	  }
	  return { fn: filterFunction, keys: keys };
	}

	// Accepts an object in the form of:
	//
	//   {
	//     key: value,
	//     key: function(val) { ... }
	//   }
	//
	// and turns it into a function that accepts a model an returns a
	// boolean + a list of the keys that the function depends on.
	function createFilterFromObject(filterObj) {
	  var keys = _.keys(filterObj);

	  var filterFunctions = _.map(keys, function(key) {
	    var val = filterObj[key];
	    if (_.isFunction(val)) {
	      return convertKeyFunctionToFunction(key, val);
	    }
	    return convertKeyValueToFunction(key, val);
	  });

	  // Iterate through each of the generated filter functions. If any
	  // are false, kill the computation and return false. The function
	  // is only true if all of the subfunctions are true.
	  var filterFunction = function(model) {
	    for (var i = 0; i < filterFunctions.length; i++) {
	      if (!filterFunctions[i](model)) {
	        return false;
	      }
	    }
	    return true;
	  };

	  return createFilterObject(filterFunction, keys);
	}

	// Expects one of the following:
	//
	//   - A filter function that accepts a model + (optional) array of
	//     keys to listen to changes for or null)
	//   - An object describing a filter
	function createFilter(filter, keys) {
	  // This must go first because _.isObject(fn) === true
	  if (_.isFunction(filter)) {
	    return createFilterObject(filter, keys);
	  }

	  // If the filter is an object describing a filter, generate the
	  // appropriate function.
	  if (_.isObject(filter)) {
	    return createFilterFromObject(filter);
	  }
	}

	module.exports = createFilter;



/***/ },
/* 39 */
/***/ function(module, exports, __webpack_require__) {

	
	var _ = __webpack_require__(5);
	var Backbone =__webpack_require__(16);
	var proxyCollection = __webpack_require__(37);
	var sortedIndex = __webpack_require__(40);

	function lookupIterator(value) {
	  return _.isFunction(value) ? value : function(obj){ return obj.get(value); };
	}

	function modelInsertIndex(model) {
	  if (!this._comparator) {
	    return this._superset.indexOf(model);
	  } else {
	    return sortedIndex(this._collection.models, model, lookupIterator(this._comparator), this._reverse);
	  }
	}

	function onAdd(model) {
	  var index = modelInsertIndex.call(this, model);
	  this._collection.add(model, { at: index });
	}

	function onRemove(model) {
	  if (this.contains(model)) {
	    this._collection.remove(model);
	  }
	}

	function onChange(model) {
	  if (this.contains(model) && this._collection.indexOf(model) !== modelInsertIndex.call(this, model)) {
	    this._collection.remove(model);
	    onAdd.call(this, model);
	  }
	}

	function sort() {
	  if (!this._comparator) {
	    this._collection.reset(this._superset.toArray());
	    return;
	  }

	  // Evaluate the type of comparator based on http://backbonejs.org/#Collection-comparator
	  var newOrder;
	  if (_.isString(this._comparator) || this._comparator.length === 1) {
	    newOrder = this._superset.sortBy(this._comparator);
	  } else {
	    newOrder = this._superset.models.sort(this._comparator);
	  }
	  this._collection.reset(this._reverse ? newOrder.reverse() : newOrder);
	}

	function Sorted(superset) {
	  // Save a reference to the original collection
	  this._superset = superset;
	  this._reverse = false;
	  this._comparator = null;

	  // The idea is to keep an internal backbone collection with the paginated
	  // set, and expose limited functionality.
	  this._collection = new Backbone.Collection(superset.toArray());
	  proxyCollection(this._collection, this);

	  this.listenTo(this._superset, 'add', onAdd);
	  this.listenTo(this._superset, 'remove', onRemove);
	  this.listenTo(this._superset, 'change', onChange);
	  this.listenTo(this._superset, 'reset', sort);
	}

	var methods = {

	  setSort: function(comparator, direction) {
	    this._reverse = direction === 'desc' ? true : false;
	    this._comparator = comparator;

	    sort.call(this);

	    if (!comparator) {
	      this.trigger('sorted:remove');
	    } else {
	      this.trigger('sorted:add');
	    }

	    return this;
	  },

	  reverseSort: function() {
	    this._reverse = !this._reverse;
	    sort.call(this);

	    return this;
	  },

	  removeSort: function() {
	    this.setSort();
	    return this;
	  },

	  superset: function() {
	    return this._superset;
	  },

	  destroy: function() {
	    this.stopListening();
	    this._collection.reset([]);
	    this._superset = this._collection;
	    this.length = 0;

	    this.trigger('sorted:destroy');
	  }

	};

	// Build up the prototype
	_.extend(Sorted.prototype, methods, Backbone.Events);

	module.exports = Sorted;



/***/ },
/* 40 */
/***/ function(module, exports, __webpack_require__) {

	
	var _ = __webpack_require__(5);

	// Underscore provides a .sortedIndex function that works
	// when sorting ascending based on a function or a key, but there's no
	// way to do the same thing when sorting descending. This is a slight
	// modification of the underscore / backbone code to do the same thing
	// but descending.

	function comparatorAdapter(fieldExtractor, reverse) {
	  return function(left, right) {
	    var l = fieldExtractor(left);
	    var r = fieldExtractor(right);

	    if(l === r) return 0;

	    return reverse ? (l < r ? 1 : -1) : (l < r ? -1 : 1);
	  };
	}

	function lookupIterator(value, reverse) {
	  return value.length === 2 ? value : comparatorAdapter(value, reverse);
	}

	function sortedIndex(array, obj, iterator, reverse) {
	  iterator = iterator === null ? _.identity : lookupIterator(iterator, reverse);

	  var low = 0, high = array.length;
	  while (low < high) {
	      var mid = (low + high) >>> 1;
	    if(iterator(array[mid], obj) < 0) {
	      low = mid + 1;
	    } else {
	      high = mid;
	    }
	  }

	  return low;
	}

	module.exports = sortedIndex;


/***/ },
/* 41 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);
	var Backbone = __webpack_require__(16);
	var proxyCollection = __webpack_require__(37);

	function getPageLimits() {
	  if(this._infinite){
	    var start = 0;
	    var end = this._collection.length;
	  } else {
	    var start = this.getPage() * this.getPerPage();
	    var end = start + this.getPerPage();
	  }
	  return [start, end];
	}

	function updatePagination() {
	  var pages = getPageLimits.call(this);
	  return this._collection.reset(this.superset().slice(pages[0], pages[1]));
	}

	function infintePagination() {
	  var start = 0;
	  var end = this._collection.length + this.getPerPage();
	  return this._collection.add(this.superset().slice(start, end));
	}

	function calcPages() {
	  var perPage = this.getPerPage();
	  var length = this.superset().length - this._collection.length;

	  var totalPages = length % perPage === 0 ?
	    (length / perPage) : Math.floor(length / perPage) + 1;

	  return totalPages + 1;
	}

	function updateNumPages() {
	  var length = this.superset().length;
	  var perPage = this.getPerPage();

	  // If the # of objects can be exactly divided by the number
	  // of pages, it would leave an empty last page if we took
	  // the floor.
	  var totalPages = length % perPage === 0 ?
	    (length / perPage) : Math.floor(length / perPage) + 1;

	  var numPagesChanged = this._totalPages !== totalPages;
	  this._totalPages = totalPages;

	  if (numPagesChanged) {
	    this.trigger('paginated:change:numPages', { numPages: totalPages });
	  }

	  // Test to see if we are past the last page, and if so,
	  // move back. Return true so that we can test to see if
	  // this happened.
	  if (this.getPage() >= totalPages) {
	    this.setPage(totalPages - 1);
	    return true;
	  }
	}

	function recalculatePagination() {
	  // reset infinite page
	  this._infinite = false;

	  if (updateNumPages.call(this)) { return; }
	  updatePagination.call(this);
	}

	// Given two arrays of backbone models, with at most one model added
	// and one model removed from each, return the model in arrayA that
	// is not in arrayB or undefined.
	function difference(arrayA, arrayB) {
	  var maxLength = _.max([ arrayA.length, arrayB.length ]);

	  for (var i = 0, j = 0; i < maxLength; i += 1, j += 1) {
	    if (arrayA[i] !== arrayB[j]) {
	      if (arrayB[i-1] === arrayA[i]) {
	        j -= 1;
	      } else if (arrayB[i+1] === arrayA[i]) {
	        j += 1;
	      } else {
	        return arrayA[i];
	      }
	    }
	  }
	}

	function onAddRemove(model, collection, options) {
	  if (updateNumPages.call(this)) { return; }

	  var pages = getPageLimits.call(this);
	  var start = pages[0], end = pages[1];

	  // We are only adding and removing at most one model at a time,
	  // so we can find just those two models. We could probably rewrite
	  // `collectionDifference` to only make on pass instead of two. This
	  // is a bottleneck on the total size of collections. I was getting
	  // slow unit tests around 30,000 models / page in Firefox.
	  var toAdd = difference(this.superset().slice(start, end), this._collection.toArray());

	  var infinite = this._infinite && options.add;
	  var toRemove;

	  if(!infinite){
	    toRemove = difference(this._collection.toArray(), this.superset().slice(start, end));
	  }

	  if (toRemove) {
	    this._collection.remove(toRemove);
	  }

	  if (toAdd) {
	    this._collection.add(toAdd, {
	      at: this.superset().indexOf(toAdd) - start
	    });
	  }
	};

	function Paginated(superset, options) {
	  // Save a reference to the original collection
	  this._superset = superset;

	  // The idea is to keep an internal backbone collection with the paginated
	  // set, and expose limited functionality.
	  this._collection = new Backbone.Collection(superset.toArray());
	  this._page = 0;
	  this.setPerPage((options && options.perPage) ? options.perPage : null);

	  proxyCollection(this._collection, this);

	  this.listenTo(this._superset, 'add remove', onAddRemove);
	  this.listenTo(this._superset, 'reset sort', recalculatePagination);
	}

	var methods = {

	  removePagination: function() {
	    this._infinite = false;
	    this.setPerPage(null);
	    return this;
	  },

	  setPerPage: function(perPage) {
	    this._perPage = perPage;
	    recalculatePagination.call(this);
	    this.setPage(0);

	    this.trigger('paginated:change:perPage', {
	      perPage: perPage,
	      numPages: this.getNumPages()
	    });

	    return this;
	  },

	  setPage: function(page) {

	    // reset infinite page
	    this._infinite = false;

	    // The lowest page we could set
	    var lowerLimit = 0;
	    // The highest page we could set
	    var upperLimit = this.getNumPages() - 1;

	    // If the page is higher or lower than these limits,
	    // set it to the limit.
	    page = page > lowerLimit ? page : lowerLimit;
	    page = page < upperLimit ? page : upperLimit;
	    page = page < 0 ? 0 : page;

	    this._page = page;
	    updatePagination.call(this);

	    this.trigger('paginated:change:page', { page: page });
	    return this;
	  },

	  getPerPage: function() {
	    return this._perPage || this.superset().length || 1;
	  },

	  getNumPages: function() {
	    if(this._infinite){
	      return calcPages.call(this);
	    } else {
	      return this._totalPages;
	    }
	  },

	  getPage: function() {
	    return this._page;
	  },

	  hasNextPage: function() {
	    return this.getPage() < this.getNumPages() - 1;
	  },

	  hasPrevPage: function() {
	    return this.getPage() > 0;
	  },

	  nextPage: function() {
	    this.movePage(1);
	    return this;
	  },

	  prevPage: function() {
	    this.movePage(-1);
	    return this;
	  },

	  firstPage: function() {
	    this.setPage(0);
	  },

	  lastPage: function() {
	    this.setPage(this.getNumPages() - 1);
	  },

	  movePage: function(delta) {
	    this.setPage(this.getPage() + delta);
	    return this;
	  },

	  superset: function() {
	    return this._superset;
	  },

	  destroy: function() {
	    this.stopListening();
	    this._collection.reset([]);
	    this._superset = this._collection;
	    this._page = 0;
	    this._totalPages = 0;
	    this.length = 0;
	    this._infinite = false;

	    this.trigger('paginated:destroy');
	  },

	  // infinite scroll
	  appendNextPage: function(){
	    this._infinite = true;
	    infintePagination.call(this);
	    this.trigger('paginated:change:page', { page: 0 });
	    return this;
	  }

	};

	// Build up the prototype
	_.extend(Paginated.prototype, methods, Backbone.Events);

	module.exports =  Paginated;

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);

	function proxyEvents(from, eventNames) {
	  _.each(eventNames, function (eventName) {
	    this.listenTo(from, eventName, function () {
	      var args = _.toArray(arguments);
	      args.unshift(eventName);
	      this.trigger.apply(this, args);
	    });
	  }, this);
	}

	module.exports = proxyEvents;

/***/ },
/* 43 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * query methods to extend Filtered Collection
	 */

	var _ = __webpack_require__(5);
	var query = __webpack_require__(44);

	module.exports = {

	  query: function (filterName, filter) {
	    if( _.isUndefined(filter) ) {
	      filter = filterName;
	      filterName = 'search';
	    }
	    this._query = filter;
	    if( filter === '' ){
	      return this.removeFilter(filterName);
	    }
	    return this.filterBy(filterName,
	      _.bind( query, this, filter )
	    );
	  },

	  getQuery: function(){
	    return this._query;
	  },

	  getTokens: function(){
	    return this._tokens;
	  },

	  getRemoteFilter: function(){
	    if(!this._tokens){
	      return;
	    }

	    var filter = {
	      'not_in': this.pluck('id').join(',')
	    };

	    _.each(this._tokens, function(token){

	      // simple search
	      if(token.type === 'string'){
	        filter.q = token.query;
	      }

	      // simple prefix search
	      if(token.type === 'prefix'){
	        filter[token.prefix] = token.query;
	      }

	    });

	    return filter;
	  }

	};

/***/ },
/* 44 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);
	var Parser = __webpack_require__(45);
	var parser = new Parser();

	function toType(obj) {
	  return ({}).toString.call(obj).match(/\s([a-z|A-Z]+)/)[1].toLowerCase();
	}

	/**
	 * Helper methods for matching tokens
	 */
	var methods = {

	  /**
	   * Token type `string`
	   * @return {Boolean}
	   */
	  string: function(token, model){
	    token = token || {};
	    if(!_.isString(token.query)){ return false; }

	    var attributes = _.chain(model.fields || ['title'])
	      .map(function(key){
	        return model.get(key); // allows nested get
	      })
	      .compact()
	      .value();

	    return _.any( attributes, function( attribute ) {
	      return this._partialString(attribute, token.query.toLowerCase());
	    }, this);
	  },

	  /**
	   * Token type `prefix`
	   * @return {Boolean}
	   */
	  prefix: function(token, model){
	    token = token || {};
	    if(!_.isString(token.query)){ return false; }

	    var attr = model.get(token.prefix),
	      type = toType(attr);

	    // _boolean, _array etc
	    if(this.hasOwnProperty('_' + type)){
	      return this['_' + type](attr, token.query.toLowerCase());
	    }
	  },

	  /**
	   * Token type `or`
	   * @return {Boolean}
	   */
	  or: function(token, model){
	    return _.any(token.queries, function(t){
	      return this[t.type](t, model);
	    }, this);
	  },

	  _string: function(str, value){
	    return str.toLowerCase() === value;
	  },

	  _partialString: function(str, value){
	    return str.toLowerCase().indexOf( value ) !== -1;
	  },

	  _number: function(number, value){
	    return number.toString() === value;
	  },

	  _partialNumber: function(number, value){
	    return number.toString().indexOf( value ) !== -1;
	  },

	  _boolean: function(bool, value){
	    if(value === 'true'){
	      return bool === true;
	    } else if (value === 'false'){
	      return bool === false;
	    }
	    return false;
	  },

	  _array: function(arr, value){
	    return _.any(arr, function(elem){
	      return elem.toLowerCase() === value;
	    });
	  }

	};

	function matchMaker(tokens, model){
	  // match tokens
	  // todo: all = AND, any = OR
	  return _.all(tokens, function(token){
	    return methods[token.type](token, model);
	  });
	}

	/**
	 * Match Maker
	 * return true or false for model based on Qparser tokens
	 * @param {String|Array} filter
	 * @param {Object} model
	 * @returns {Boolean}
	 */
	module.exports = function(filter, model){
	  var tokens = _.isArray(filter) ? filter : parser.parse(filter);
	  this._tokens = tokens;

	  // allow model specific match maker
	  if(model.matchMaker){
	    return model.matchMaker(tokens, methods, matchMaker);
	  } else {
	    return matchMaker(tokens, model);
	  }
	};

/***/ },
/* 45 */
/***/ function(module, exports, __webpack_require__) {

	/* jshint -W071, -W074 */
	var _ = __webpack_require__(5);

	/**
	 *
	 * @param options
	 * @constructor
	 */
	function Parser(options){
	  this.options = options || {};
	}

	/**
	 * Regex for special characters
	 */
	var regex = {
	  QUOTES      : /['"`]/,       // quotes
	  SPACES      : /[ \t\r\n]/,   // spaces
	  FLAGS       : /[~\+#!\*\/]/, // flags
	  SCREEN      : /[\\]/,        // screen
	  GROUP_OPEN  : /\(/,          // group openers
	  GROUP_CLOSE : /\)/,          // group endings
	  OR          : /\|/,          // logical OR
	  PREFIX      : /:/,           // divider between prefix and value
	  RANGE       : /-/,           // divider between values in range
	  OR_OPEN     : /\[/,          // OR group openers
	  OR_CLOSE    : /]/            // OR group endings
	};

	/**
	 * Returns first regex match for given character
	 * note: order is important!
	 * @param character
	 */
	function matchRegex(character){
	  var match;

	  _.any([
	    'SCREEN',
	    'OR_OPEN',
	    'OR_CLOSE',
	    'GROUP_OPEN',
	    'GROUP_CLOSE',
	    'OR',
	    'PREFIX',
	    //'RANGE',
	    'SPACES',
	    'QUOTES',
	    'FLAGS'
	  ], function(key){
	    if(regex[key].test(character)){
	      match = key; return true;
	    } else {
	      match = undefined; return false;
	    }
	  });

	  return match;
	}

	/**
	 *
	 */
	function logicalOr(parts){
	  var p2 = parts.pop(),
	      p1 = parts.pop();

	  parts.push({
	    type: 'or',
	    queries: [ p1, p2 ]
	  });
	}

	/**
	 *
	 * @param options
	 */
	function appendPart(opts){
	  var part = opts.part || {};

	  if(!opts.hasarg){ return; }

	  if (['range', 'prange'].indexOf(part.type) >= 0) {
	    part.to = opts.buffer;
	  } else if (opts.buffer && opts.buffer.length) {
	    part.query = opts.buffer;
	  }

	  if (!part.type) {
	    part.type = part.prefix ? 'prefix' : 'string';
	  }

	  opts.parts.push(part);

	  if (opts.or_at_next_arg && (opts.or_at_next_arg + 1 === opts.parts.length)){
	    logicalOr(opts.parts);
	    opts.or_at_next_arg = 0;
	  }

	  opts.part = {};
	  opts.buffer = '';
	  opts.hasarg = false;

	}

	/**
	 *
	 * @param options
	 * @param quote
	 */
	function inQuote(opts, quote){
	  if(this._input.length === 0){
	    return;
	  }

	  opts.character = this._input.shift();

	  if (opts.character === quote) {
	    appendPart.call(this, opts);
	  } else {
	    opts.buffer += opts.character;
	    opts.hasarg = true;
	    inQuote.call(this, opts, quote);
	  }
	}

	/**
	 *
	 */
	var matches = {

	  screen: function(opts){
	    opts.screen = true;
	  },

	  or_open: function(opts){
	    if (opts.hasarg) {
	      opts.buffer += opts.character;
	    } else {
	      opts.part.type = 'or';
	      opts.part.queries = this.parse(this._input.join(''), true);
	      if (opts.part.queries && opts.part.queries.length) {
	        opts.hasarg = true;
	        appendPart.call(this, opts);
	      }
	    }
	  },

	  or_close: function(opts){
	    opts.close = true;
	  },

	  group_open: function(opts){
	    if (opts.hasarg) {
	      opts.buffer += opts.character;
	    } else {
	      opts.part.type = 'and';
	      opts.part.queries = this.parse(this._input.join(''), true);
	      if (opts.part.queries && opts.part.queries.length) {
	        opts.hasarg = true;
	        appendPart.call(this, opts);
	      }
	    }
	  },

	  group_close: function(opts){
	    if(opts.open){
	      opts.close = true;
	      opts.open = undefined;
	    } else {
	      opts.buffer += opts.character;
	    }
	  },

	  or: function(opts){
	    opts.or_at_next_arg = opts.parts.length;
	    if (opts.hasarg) {
	      opts.or_at_next_arg += 1;
	      appendPart.call(this, opts);
	    }
	  },

	  prefix: function(opts){
	    opts.part.prefix = opts.buffer;
	    opts.part.type = 'prefix';
	    opts.buffer = '';
	    opts.hasarg = true;
	  },

	  range: function(opts){
	    if (opts.part.type && (opts.part.type === 'prefix')) {
	      opts.part.type = 'prange';
	    } else {
	      opts.part.type = 'range';
	    }
	    opts.part.from = opts.buffer;
	    opts.buffer = '';
	    opts.hasarg = true;
	  },

	  spaces: function(opts){
	    appendPart.call(this, opts);
	  },

	  quotes: function(opts){
	    if (opts.buffer.length) {
	      opts.buffer += opts.character;
	      opts.hasarg = true;
	    } else {
	      inQuote.call(this, opts, opts.character);
	    }
	  },

	  flags: function(opts){
	    if (!opts.buffer.length) {
	      if (!opts.part.flags) { opts.part.flags = []; }
	      opts.part.flags.push(opts.character);
	    } else {
	      opts.buffer += opts.character;
	    }
	  }
	};

	/**
	 *
	 * @param options
	 */
	function next(opts){
	  opts.character = this._input.shift();
	  var match = matchRegex.call(this, opts.character);
	  if(match && !opts.screen){
	    matches[match.toLowerCase()].call(this, opts);
	  } else {
	    opts.buffer += opts.character;
	    opts.hasarg = true;
	    opts.screen = false;
	  }
	  if(this._input.length > 0 && !opts.close){
	    next.call(this, opts);
	  } else {
	    opts.close = undefined;
	    return;
	  }
	}

	/**
	 *
	 */
	var methods = {

	  parse: function(input, open) {
	    var opts = {
	      parts   : [],
	      part    : {},
	      open    : open,
	      buffer  : '',
	      hasarg  : false
	    };

	    if (!input || !input.length || (typeof input !== 'string')) {
	      return opts.parts;
	    }

	    this._input = input.split('');
	    next.call(this, opts);
	    appendPart.call(this, opts);
	    return opts.parts;
	  }

	};

	_.extend(Parser.prototype, methods);

	module.exports = Parser;
	/* jshint +W071, +W074 */

/***/ },
/* 46 */
/***/ function(module, exports, __webpack_require__) {

	var DualCollection = __webpack_require__(22);
	var Model = __webpack_require__(47);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var bb = __webpack_require__(16);

	module.exports = DualCollection.extend({
	  model: Model,
	  name: 'orders',
	  _syncDelayed: false,

	  /**
	   * Open orders first
	   */
	  //comparator: function( model ){
	  //  if( model.get('id') === undefined ) { return 0; }
	  //  return 1;
	  //},

	  /**
	   *
	   */
	  setActiveOrder: function(id){
	    var order = this.get(id);

	    if( !order && id !== 'new' ){
	      order = _.first( this.openOrders() );
	    }

	    this.active = order;
	    return order;
	  },

	  /**
	   * Promise of an active order
	   */
	  getActiveOrder: function(){
	    var self = this;
	    var deferred = new $.Deferred();

	    if(!this.active){
	      this.create().then(function(order){
	        order.cart.order_id = order.id;
	        self.active = order;
	        if(bb.history.getHash() === 'cart/new') {
	          bb.history.navigate('cart/' + order.id);
	        }
	        deferred.resolve(order);
	      });
	    } else {
	      deferred.resolve(this.active);
	    }

	    return deferred.promise();
	  },

	  addToCart: function(options){
	    this.getActiveOrder()
	      .then(function(order){
	        order.cart.addToCart(options);
	      });
	  },

	  create: function(){
	    var deferred = new $.Deferred();

	    // Safari has a problem with create, perhaps an autoincrement problem?
	    // Set local_id as timestamp milliseconds
	    DualCollection.prototype.create.call(this, { local_id: Date.now() }, {
	      wait: true,
	      success: deferred.resolve,
	      error: deferred.reject
	    });

	    return deferred.promise();
	  },

	  /**
	   *
	   */
	  openOrders: function(){
	    return this.filter(function(model){
	      return model.isEditable();
	    });
	  }

	});

/***/ },
/* 47 */
/***/ function(module, exports, __webpack_require__) {

	//var DualModel = require('lib/config/dual-model');
	var DualModel = __webpack_require__(29);
	var Radio = __webpack_require__(4);
	var Utils = __webpack_require__(8);
	var debug = __webpack_require__(13)('order');
	var App = __webpack_require__(2);
	var $ = __webpack_require__(6);

	var Model = DualModel.extend({
	  name: 'order',
	  fields: [
	    'customer.first_name',
	    'customer.last_name',
	    'customer.email'
	  ],

	  /**
	   * Orders with the following status are closed for editing
	   */
	  //closedStatus: [
	  //  'completed',
	  //  'on-hold',
	  //  'cancelled',
	  //  'refunded',
	  //  'processing',
	  //  'failed'
	  //],

	  /**
	   *
	   */
	  defaults: {
	    note            : '',
	    order_discount  : 0
	  },

	  /**
	   * - attach tax settings
	   * - attach cart & gateways if order is open
	   */
	  /* jshint -W071, -W074 */
	  initialize: function(attributes){
	    attributes = attributes || {};

	    if(!attributes.customer){
	      var customers = this.getEntities('customers');
	      var customer = customers['default'] || customers.guest || {};
	      this.set({
	        customer_id : customer.id,
	        customer    : customer
	      });
	    }

	    this.tax = this.getEntities('tax');
	    this.tax_rates = this.getEntities('tax_rates');

	    if( this.isEditable() ){
	      this.attachCart();
	      this.attachGateways();
	    }

	    // order_discount input
	    this.on({
	      'change:order_discount': this.calcTotals,
	      'change:status': this.isEditable
	    });

	  },
	  /* jshint +W071, +W074 */

	  getEntities: function(name){
	    return Radio.request('entities', 'get', {
	      type: 'option',
	      name: name
	    }) || {};
	  },

	  /**
	   * is order editable method, sets _open true or false
	   */
	  isEditable: function(){
	    //return !_.contains(this.closedStatus, this.get('status'));
	    return this.get('status') === undefined || this.isDelayed();
	    //return this.isDelayed();
	  },

	  /**
	   * Remove items from cart before destroy
	   */
	  destroy: function(options){
	    var self = this;
	    return this.cart.db.removeBatch( this.cart.pluck('local_id') )
	      .always(function(){
	        return DualModel.prototype.destroy.call(self, options);
	      });
	  },

	  /**
	   * Attach cart
	   */
	  attachCart: function(){
	    var cart = Radio.request('entities', 'get', {
	      init : true,
	      type : 'collection',
	      name : 'cart'
	    });

	    cart.order_id = this.id;

	    this.listenTo(cart, {
	      'add change' : this.calcTotals,
	      'remove'     : this.itemRemoved
	    });

	    if(cart.db){
	      cart.db.open().then(function(){
	        cart.fetchCartItems();
	      });
	    }

	    this.cart = cart;
	  },

	  /**
	   * remove cart items from idb after successful order
	   */
	  clearCart: function(){
	    if(this.cart){
	      this.cart.db.removeBatch( this.cart.pluck('local_id') );
	    }
	  },

	  /**
	   * Attach gateways
	   */
	  attachGateways: function(){
	    this.gateways = Radio.request('entities', 'get', {
	      init : true,
	      type : 'collection',
	      name : 'gateways'
	    });

	    var gateways = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'gateways'
	    });
	    this.gateways.add(gateways);
	  },

	  /**
	   *
	   */
	  itemRemoved: function(){
	    if(this.cart.length > 0){
	      return this.calcTotals();
	    }
	    return this.destroy();
	  },

	  /**
	   * Sum cart totals
	   * todo: too many statements
	   */
	  /* jshint -W071 */
	  calcTotals: function(){
	    var total_tax     = 0,
	        subtotal_tax  = 0,
	        shipping_tax  = 0,
	        cart_discount_tax = 0,
	        subtotal      = this.cart.sum('subtotal'),
	        total         = this.cart.sum('total'),
	        cart_discount = subtotal - total,
	        order_discount = this.get('order_discount');

	    if( this.tax.calc_taxes === 'yes' ) {
	      total_tax         = this.cart.sum('total_tax');
	      subtotal_tax      = this.cart.sum('subtotal_tax');
	      shipping_tax      = this.cart.sum('total_tax', 'shipping');
	      cart_discount_tax = subtotal_tax - total_tax;
	    }

	    total += total_tax;
	    total -= order_discount;

	    // tax_lines will merge the data - possibly due to deep model
	    // clear tax_lines before save to ensure clean data
	    this.unset('tax_lines', { silent: true });

	    // create totals object
	    var totals = {
	      'total'             : Utils.round( total, 4 ),
	      'subtotal'          : Utils.round( subtotal, 4 ),
	      'total_tax'         : Utils.round( total_tax, 4 ),
	      'subtotal_tax'      : Utils.round( subtotal_tax, 4 ),
	      'shipping_tax'      : Utils.round( shipping_tax, 4 ),
	      'cart_discount'     : Utils.round( cart_discount, 4 ),
	      'cart_discount_tax' : Utils.round( cart_discount_tax, 4 ),
	      'tax_lines'         : this.cart.itemizedTax()
	    };

	    this.save(totals);
	    debug('update totals', totals);
	  },
	  /* jshint +W071 */

	  /**
	   * Convenience method to sum attributes
	   */
	  sum: function(array){
	    var sum = 0;
	    for (var i = 0; i < array.length; i++) {
	      sum += parseFloat( this.get(array[i]) );
	    }
	    return sum;
	  },

	  /**
	   * process order
	   * todo: remoteSync resolves w/ an array of models, should match sync?
	   */
	  process: function(){
	    var self = this;

	    return $.when( this.processCart() )
	      .then(function(){
	        return self.processGateway();
	      })
	      .then(function(){
	        var method = self.get('id') ? 'update' : 'create';
	        return self.remoteSync(method);
	      })
	      .then(function(array){
	        var model = array[0];
	        if(model.get('status') === 'failed'){
	          model.save({ status: 'UPDATE_FAILED' });
	        }
	      });
	  },

	  /**
	   *
	   */
	  processCart: function(){
	    var obj = {
	      product : [],
	      shipping: [],
	      fee     : []
	    };

	    this.cart.each(function(model){
	      var type = model.get('type');
	      if(type !== 'shipping' && type !== 'fee'){
	        type = 'product';
	      }
	      obj[type].push( model.toJSON() );
	    });

	    // set
	    this.set({
	      line_items    : obj.product,
	      shipping_lines: obj.shipping,
	      fee_lines     : obj.fee,
	      tax_lines     : this.cart.itemizedTax() // reset for retry
	    });
	  },

	  /**
	   *
	   */
	  processGateway: function(){
	    var data = this.gateways.findWhere({ active: true }).toJSON();
	    this.set({
	      payment_details: data
	    });
	  }

	});

	module.exports = Model;
	App.prototype.set('Entities.Order.Model', Model);

/***/ },
/* 48 */
/***/ function(module, exports, __webpack_require__) {

	var IndexedDBCollection = __webpack_require__(23);
	var Model = __webpack_require__(49);
	var _ = __webpack_require__(5);
	var bb = __webpack_require__(16);

	module.exports = IndexedDBCollection.extend({
	  model: Model,
	  name: 'cart',
	  indexes: [
	    {name: 'local_id', keyPath: 'local_id', unique: true},
	    {name: 'order', keyPath: 'order', unique: false},
	    {name: 'type', keyPath: 'type', unique: false}
	  ],

	  /**
	   * Whitelist of attributes taken from product model
	   */
	  productAttributes: [
	    'order',
	    'title',
	    'local_id',
	    'product_id',
	    'type',
	    'price',
	    'regular_price',
	    'sale_price',
	    'taxable',
	    'tax_status',
	    'tax_class',
	    'attributes',
	    'meta',         // variation meta
	    'method_title', // shipping
	    'method_id'     // shipping
	  ],

	  comparator: function( model ){
	    var type = model.get( 'type' );
	    if( type === 'fee' ) { return 2; }
	    if( type === 'shipping' ) { return 1; }
	    return 0;
	  },

	  /**
	   * If collection has order_id, query idb for index: 'order' = order_id
	   * onSuccess add items to collection
	   */
	  fetchCartItems: function () {
	    if(!this.order_id){
	      return;
	    }

	    var onSuccess = this.add.bind(this);
	    var keyRange = this.db.store.makeKeyRange({
	      only: this.order_id
	    });

	    this.db.store.query(onSuccess, {
	      index: 'order',
	      keyRange: keyRange
	    });
	  },

	  // convenience method to sum attributes in collection
	  sum: function(attribute, type){
	    var col = this.toJSON();
	    if(type){ col = _.where(col, {type: type}); }
	    return _.pluck(col, attribute).reduce(function(a, b){return a + b;}, 0);
	  },

	  /**
	   * add/increase item
	   * also prune attributes
	   */
	  /* jshint -W071, -W074 */
	  addToCart: function(options){
	    options = options || {};
	    var model, attributes = options.model || options;
	    if(attributes instanceof bb.Model){
	      attributes = attributes.toJSON();
	    }

	    if(attributes.id){
	      model = this.findWhere({ product_id: attributes.id });
	      attributes.product_id = attributes.id;
	      delete attributes.id;
	    }

	    if(model){
	      model.quantity('increase');
	    } else {
	      model = this._addToCart(attributes);
	    }

	    model.trigger('pulse');
	  },
	  /* jshint +W071, +W074 */

	  _addToCart: function(attributes){
	    attributes.order = this.order_id;

	    // turn variation attributes into line item meta
	    if(attributes.type === 'variation'){
	      attributes.meta = _.map(attributes.attributes, function(variant, idx){
	        return {
	          key: ++idx,
	          label: variant.name,
	          value: variant.option
	        };
	      });
	    }

	    return this.add(_.pick(attributes, this.productAttributes));
	  },

	  itemizedTax: function(){
	    var items = _.clone(this.toJSON(), true);
	    var taxes = _.map(items, function(item){
	      if(!item.tax) { return; }
	      _.each(item.tax, function(tax){
	        tax.shipping = item.type === 'shipping' ? tax.total : 0 ;
	      });
	      return item.tax;
	    });
	    var obj = this.sumTaxes(taxes);

	    // convert obj to array to be consistent with WC REST API output
	    var arr = [];
	    _.each(obj, function(value, key){
	      //value.rate_id = parseInt(key, 10);
	      value.rate_id = key.toString(); // make sure it's a string
	      arr.push(value);
	    });

	    return arr;
	  },

	  sumTaxes: function(taxes){
	    return _.reduce(taxes, function(result, tax){
	      return _.merge(result, tax, function(a, b){
	        if(a){
	          b.total += a.total;
	          b.subtotal += a.subtotal;
	          b.shipping += a.shipping;
	        }
	        return b;
	      });
	    }, {});
	  }

	});

/***/ },
/* 49 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(31);
	var debug = __webpack_require__(13)('cartItem');
	var Utils = __webpack_require__(8);
	var _ = __webpack_require__(5);
	var Radio = __webpack_require__(4);

	module.exports = Model.extend({
	  idAttribute: 'local_id',

	  defaults : {
	    'subtotal'      : 0,
	    'subtotal_tax'  : 0,
	    'total_tax'     : 0,
	    'total'         : 0,
	    'item_tax'      : 0,
	    'quantity'      : 1,
	    'taxable'       : true,
	    'tax_class'     : ''
	  },

	  /* jshint -W074 */
	  initialize: function() {

	    // attach tax settings
	    this.tax = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tax'
	    }) || {};
	    this.tax_rates = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tax_rates'
	    }) || {};

	    // update on change to quantity, item_price ...
	    this.on(
	      'change:quantity ' +
	      'change:item_price ' +
	      'change:regular_price ' +
	      'change:taxable ' +
	      'change:tax_class',
	      this.updateLineTotals );

	    // set item price on init, this wil kick off updateLineTotals
	    if( this.get('item_price') === undefined ) {
	      var price = parseFloat( this.get('price') );
	      this.set({ 'item_price': _.isNaN(price) ? 0 : price });
	    }
	  },
	  /* jshint +W074 */

	  /* jshint -W071, -W074 */
	  /* todo: too many statements, too complex */
	  updateLineTotals: function() {
	    var quantity        = this.get('quantity'),
	        item_price      = this.get('item_price'),
	        type            = this.get('type'),
	        regular_price   = parseFloat( this.get('regular_price')),
	        tax_class       = this.get('tax_class'),
	        item_tax,
	        item_subtotal_tax,
	        rates;

	    // make a copy of the tax rates for this product
	    if(this.tax_rates[tax_class]){
	      rates = _.cloneDeep(this.tax_rates[tax_class]);
	    }

	    // if shipping or fee
	    if( type === 'shipping' || type === 'fee' ) {
	      regular_price = item_price;
	    }

	    // calc taxes
	    item_tax = this.calcTax({
	      price    : item_price,
	      quantity : quantity,
	      rates    : rates
	    });

	    item_subtotal_tax = this.calcTax({
	      price    : regular_price,
	      quantity : quantity,
	      rates    : rates,
	      subtotal : true
	    });

	    // if price does not include tax
	    if( this.tax.prices_include_tax === 'yes' ) {
	      regular_price -= item_subtotal_tax;
	      item_price -= item_tax;
	    }

	    // create totals object
	    var totals = {
	      'item_subtotal'     : Utils.round( regular_price, 4 ),
	      'item_subtotal_tax' : Utils.round( item_subtotal_tax, 4 ),
	      'item_tax'          : Utils.round( item_tax, 4 ),
	      'subtotal'          : Utils.round( regular_price * quantity, 4 ),
	      'subtotal_tax'      : Utils.round( item_subtotal_tax * quantity, 4 ),
	      'total_tax'         : Utils.round( item_tax * quantity, 4 ),
	      'total'             : Utils.round( item_price * quantity, 4 )
	    };

	    this.save(totals);
	    debug('update totals', totals);
	  },
	  /* jshint +W071, +W074 */

	  /**
	   * Calculate the line item tax total
	   * based on the calc_tax function in woocommerce/includes/class-wc-tax.php
	   */
	  calcTax: function(options) {
	    var item_tax = 0;

	    if(this.tax.calc_taxes === 'yes' && this.get('taxable') && options.rates) {
	      if( this.tax.prices_include_tax === 'yes' ) {
	        item_tax = this.calcInclusiveTax(options);
	      } else {
	        item_tax = this.calcExclusiveTax(options);
	      }
	    } else {
	      this.set('tax', undefined);
	    }

	    return item_tax;
	  },

	  /**
	   * Calculate the line item tax total
	   * based on the calc_inclusive_tax function in
	   * woocommerce/includes/class-wc-tax.php
	   */
	  /* todo: too many statements */
	  /* jshint -W071 */
	  calcInclusiveTax: function(options) {
	    var regular_tax_rates = 0,
	        compound_tax_rates = 0,
	        non_compound_price = 0,
	        tax_amount = 0,
	        item_tax = 0,
	        price = options.price,
	        rates = options.rates,
	        qty = options.quantity;

	    _.each(rates, function(rate, key) {
	      if( this.get('type') === 'shipping' && rate.shipping === 'no' ){
	        delete rates[key];
	        return;
	      }
	      if ( rate.compound === 'yes' ) {
	        compound_tax_rates = compound_tax_rates + parseFloat(rate.rate);
	      } else {
	        regular_tax_rates = regular_tax_rates + parseFloat(rate.rate);
	      }
	    }, this);

	    var regular_tax_rate  = 1 + ( regular_tax_rates / 100 );
	    var compound_tax_rate   = 1 + ( compound_tax_rates / 100 );
	    non_compound_price = price / compound_tax_rate;

	    _.each(rates, function(rate) {
	      var the_rate = parseFloat(rate.rate) / 100;
	      var the_price = 0;

	      if ( rate.compound === 'yes' ) {
	        the_price = price;
	        the_rate  = the_rate / compound_tax_rate;
	      }  else {
	        the_price = non_compound_price;
	        the_rate  = the_rate / regular_tax_rate;
	      }

	      var net_price = price - ( the_rate * the_price );
	      tax_amount = price - net_price;

	      // set the itemized taxes
	      var prop = options.subtotal ? 'subtotal' : 'total';
	      rate[prop] = Utils.round( tax_amount * qty, 4 );

	      // sum item taxes
	      item_tax += tax_amount;

	    }, this);

	    // itemized tax
	    if( !_.isEmpty(rates) ){
	      this.set('tax', rates);
	    }

	    // return the item tax
	    return item_tax;
	  },
	  /* jshint +W071 */

	  /**
	   * Calculate the line item tax total
	   * based on the calc_exclusive_tax function in
	   * woocommerce/includes/class-wc-tax.php
	   */
	  calcExclusiveTax: function(options) {
	    var taxes = [],
	        pre_compound_total = 0,
	        tax_amount = 0,
	        item_tax = 0,
	        price = options.price,
	        rates = options.rates,
	        qty = options.quantity;

	    // multiple taxes
	    _.each(rates, function(rate, key) {
	      tax_amount = 0;
	      if( this.get('type') === 'shipping' && rate.shipping === 'no' ){
	        delete rates[key];
	        return;
	      }
	      if ( rate.compound !== 'yes' ) {
	        tax_amount = price * ( parseFloat(rate.rate) / 100 );
	      }
	      taxes[ key ] = tax_amount;
	    }, this);

	    if( taxes.length > 0 ) {
	      pre_compound_total = taxes.reduce(function(sum, num) {return sum + num;});
	    }

	    // compound taxes
	    _.each(rates, function(rate, key) {
	      if ( rate.compound === 'yes' ) {
	        var the_price_inc_tax = price + pre_compound_total;
	        taxes[ key ] = the_price_inc_tax * ( parseFloat(rate.rate) / 100 );
	      }

	      // set the itemized taxes
	      var prop = options.subtotal ? 'subtotal' : 'total';
	      rate[prop] = Utils.round( taxes[ key ] * qty, 4 );

	      // sum item taxes
	      item_tax += taxes[ key ];

	    }, this);

	    // itemized tax
	    if( !_.isEmpty(rates) ){
	      this.set('tax', rates);
	    }

	    // return the item tax
	    return item_tax;
	  },

	  // Convenience method to increase or decrease quantity
	  quantity: function( type ) {
	    var quantity = this.get('quantity');
	    this.set('quantity', (type === 'increase' ? ++quantity : --quantity) );
	    return this;
	  },

	  // Convenience method to sum attributes
	  sum: function(array){
	    var sum = 0;
	    for (var i = 0; i < array.length; i++) {
	      sum += this.get(array[i]);
	    }
	    return Utils.round(sum, 4);
	  }

	});

/***/ },
/* 50 */
/***/ function(module, exports, __webpack_require__) {

	var DualCollection = __webpack_require__(22);
	var Model = __webpack_require__(51);
	var Radio = __webpack_require__(4);

	module.exports = DualCollection.extend({
	  model: Model,
	  name: 'customers',
	  indexes: [
	    {name: 'local_id', keyPath: 'local_id', unique: true},
	    {name: 'id', keyPath: 'id', unique: true},
	    {name: 'status', keyPath: 'status', unique: false},
	    {name: 'email', keyPath: 'email', unique: true},
	    {name: 'username', keyPath: 'username', unique: true}
	  ],

	  initialize: function(){
	    var settings = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'customers'
	    });
	    if(settings){
	      this._guest = settings.guest;
	      this._default = settings['default'] || settings.guest;
	    }
	  },

	  getGuestCustomer: function(){
	    return this._guest;
	  },

	  getDefaultCustomer: function(){
	    return this._default;
	  }

	});

/***/ },
/* 51 */
/***/ function(module, exports, __webpack_require__) {

	var DualModel = __webpack_require__(29);
	var App = __webpack_require__(2);

	var CustomersModel = DualModel.extend({
	  name: 'customer',
	  // this is an array of fields used by FilterCollection.matchmaker()
	  fields: ['email', 'username', 'first_name', 'last_name']
	});

	module.exports = CustomersModel;
	App.prototype.set('Entities.Customers.Model', CustomersModel);

/***/ },
/* 52 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(24);
	var Model = __webpack_require__(53);

	module.exports = Collection.extend({
	  model: Model
	});

/***/ },
/* 53 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(31);

	module.exports = Model.extend({});

/***/ },
/* 54 */
/***/ function(module, exports, __webpack_require__) {

	var DeepModel = __webpack_require__(30);
	var Radio = __webpack_require__(4);
	var polyglot = __webpack_require__(10);

	module.exports = DeepModel.extend({

	  initialize: function() {
	    this.url = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'ajaxurl'
	    });
	  },

	  sync: function (method, model, options) {
	    var nonce = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'nonce'
	    });

	    var id       = 'id=' + model.get('id'),
	        action   = 'action=wc_pos_admin_settings',
	        security = 'security=' + nonce;

	    //options.emulateHTTP = true;
	    options.url = this.url + '?' + action + '&' + id + '&' + security;

	    // button state
	    if(options.buttons){
	      this.updateButtonState(options);
	    }

	    return DeepModel.prototype.sync(method, model, options);
	  },

	  parse: function (resp) {
	    // ajax will return false if no option exists
	    if(!resp){ resp = null; }
	    return resp;
	  },

	  updateButtonState: function(options){
	    var success = options.success,
	        error = options.error,
	        btn = options.buttons;

	    btn.trigger('state', [ 'loading', '' ]);

	    options.success = function(model, resp, options){
	      if( success ) { success(model, resp, options); }
	      btn.trigger('state', [ 'success', null ]);
	    };

	    options.error = function(jqxhr, textStatus, errorThrown){
	      if( error ) { error(jqxhr, textStatus, errorThrown); }
	      var message = null;

	      // code 405 = not allowed HTTP methods
	      if( jqxhr.status && jqxhr.status === 405 ){
	        message = polyglot.t('messages.legacy') +
	            '. <a href="#tools">' + polyglot.t('buttons.legacy') + '</a>.';
	      }

	      // other errors
	      if( !message && jqxhr.responseJSON && jqxhr.responseJSON.errors ){
	        message = jqxhr.responseJSON.errors[0].message;
	      }
	      btn.trigger('state', ['error', message]);
	    };
	  },

	  /**
	   * Override destroy to restore data
	   * @param options
	   * @returns {*}
	   */
	  destroy: function(options){
	    var self = this;
	    return this.sync('delete', this, options)
	      .then(function(data){
	        data.id = self.id;
	        self.clear({ silent: true }).set(data);
	      });
	  }

	});

/***/ },
/* 55 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(24);
	var Model = __webpack_require__(54);

	module.exports = Collection.extend({
	  model: Model
	});

/***/ },
/* 56 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(24);
	var Model = __webpack_require__(57);

	module.exports = Collection.extend({
	  model: Model,

	  initialize: function() {
	    this._isNew = false;
	    this.on( 'change:active', this.onChangeActive );
	  },

	  onChangeActive: function(model, active) {
	    if(!active){ return; }
	    this.each( function(tab) {
	      if( model.id !== tab.id ) {
	        tab.set({ active: false });
	      }
	    });
	  }
	});

/***/ },
/* 57 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(31);

	module.exports = Model.extend({
	  idAttribute: 'method_id',

	  defaults: {
	    active: false
	  }
	});

/***/ },
/* 58 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var Backbone = __webpack_require__(16);
	var LayoutView = __webpack_require__(59);
	var AlertView = __webpack_require__(66);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var globalChannel = __webpack_require__(4).channel('global');

	module.exports = Service.extend({
	  channelName: 'modal',

	  initialize: function(options){
	    this.container = options.container;
	    this.start();
	  },

	  onStart: function(){
	    this.channel.reply({
	      'open'    : this.open,
	      'close'   : this.close,
	      'alert'   : this.alert,
	      'confirm' : this.confirm,
	      'prompt'  : this.prompt
	    }, this);

	    this.layout = new LayoutView();
	    this.container.show(this.layout);

	    this.channel.reply({
	      'update': this.layout.update
	    }, this.layout);

	    globalChannel.on({
	      'error'   : this.error
	    }, this);

	    this.listenTo(Backbone.history, {
	      'route' : this.onRoute
	    });
	  },

	  onStop: function(){
	    delete this.layout;
	    this.container.reset();
	    this.channel.reset();
	  },

	  onRoute: function(){
	    if (this.fragment !== Backbone.history.fragment) {
	      this.close();
	    }
	  },

	  //alert: function(options){
	  //  var deferred = $.Deferred();
	  //  var view = new AlertView(options);
	  //
	  //  view.on({
	  //    'confirm' : deferred.resolve,
	  //    'cancel'  : deferred.resolve
	  //  });
	  //
	  //  return deferred;
	  //},
	  //
	  //confirm: function(options){
	  //  var deferred = $.Deferred();
	  //  var view = new ConfirmView(options);
	  //
	  //  view.on({
	  //    'confirm' : deferred.resolve,
	  //    'cancel'  : deferred.reject
	  //  });
	  //
	  //  return deferred;
	  //},
	  //
	  //prompt: function(options){
	  //  var deferred = $.Deferred();
	  //  var view = new PromptView(options);
	  //
	  //  view.on({
	  //    'submit' : deferred.resolve,
	  //    'cancel' : deferred.reject
	  //  });
	  //
	  //  return deferred;
	  //},

	  open: function(view){
	    var self = this;
	    this.fragment = Backbone.history.fragment;
	    return this.close().then(function() {
	      self.isOpen = true;
	      return self.layout.open(view);
	    });
	  },

	  close: function(){
	    if (this.isOpen) {
	      this.isOpen = false;
	      return this.layout.close();
	    } else {
	      return $.Deferred().resolve();
	    }
	  },

	  error: function(options){
	    options = options || {};

	    if(options.jqXHR){
	      this.parseXHR(options);
	    }

	    var view = new AlertView({
	      className : 'error',
	      title     : options.status,
	      message   : options.message,
	      raw       : options.raw
	    });

	    this.open(view);
	  },

	  parseXHR: function(options){
	    if( _.isObject(options.thrownError) ){
	      options.status = options.thrownError.name;
	      options.message = options.thrownError.message;
	    } else {
	      options.status = options.jqXHR.statusText;
	      if( options.jqXHR.responseJSON && options.jqXHR.responseJSON.errors[0] ){
	        options.message = options.jqXHR.responseJSON.errors[0].message;
	      }
	    }
	    options.raw = options.jqXHR.responseText;
	  }

	});

/***/ },
/* 59 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var Header = __webpack_require__(60);
	var _ = __webpack_require__(5);
	var $ = __webpack_require__(6);
	var hbs = __webpack_require__(7);
	var Tmpl = __webpack_require__(63);
	var Radio = __webpack_require__(4);
	var debug = __webpack_require__(13)('modalLayout');
	var App = __webpack_require__(2);
	__webpack_require__(64);

	module.exports = LayoutView.extend({
	  template: hbs.compile(Tmpl),

	  // if wp-admin, add css prefix
	  className: function(){
	    return App.prototype.namespace('modal');
	  },

	  attributes: {
	    'tabindex' : -1,
	    'role' : 'dialog'
	  },

	  buttons: [
	    {
	      type: 'message'
	    },{
	      action: 'save',
	      icon: 'prepend',
	      className: 'btn-primary'
	    }
	  ],

	  regions: {
	    header  : '.modal-header',
	    content : '.modal-body',
	    footer  : '.modal-footer'
	  },

	  initialize: function () {
	    this.$el.modal({ show: false, backdrop: 'static' });
	  },

	  events: {
	    'click [data-action="close"]' : function(e){
	      e.preventDefault();
	      Radio.request('modal', 'close');
	    }
	  },

	  triggers: {
	    'show.bs.modal'   : { preventDefault: false, event: 'before:open' },
	    'shown.bs.modal'  : { preventDefault: false, event: 'open' },
	    'hide.bs.modal'   : { preventDefault: false, event: 'before:close' },
	    'hidden.bs.modal' : { preventDefault: false, event: 'close' }
	  },

	  open: function(view){
	    var deferred = $.Deferred();
	    this.once('open', deferred.resolve);
	    this.setup(view);
	    this.content.show(view);
	    this.$el.modal('show');
	    return deferred;
	  },

	  close: function() {
	    var deferred = $.Deferred();
	    this.once('close', function() {
	      this.tearDown();
	      deferred.resolve();
	    });
	    this.$el.modal('hide');
	    return deferred;
	  },

	  setup: function(view){
	    var attributes = view.modal || {};

	    _.defaults(attributes, {
	      header: {},
	      footer: {}
	    });

	    _.each(attributes, function(attr, key){
	      var method = $.camelCase('modal-' + key);
	      if(this[method]){
	        this[method](attr);
	      } else {
	        debug('no method matching ' + method);
	      }
	    }, this);
	  },

	  tearDown: function(){
	    this.header.empty();
	    this.content.empty();
	    this.footer.empty();
	    this.$('.modal-dialog').removeClass().addClass('modal-dialog');
	  },

	  update: function(options){
	    options = options || {};
	    _.each(options, function(attr, key){
	      this[key].currentView.triggerMethod('Update', attr);
	    }, this);
	  },

	  modalHeader: function(options){
	    var view = new Header(options);
	    this.header.show(view);
	  },

	  modalFooter: function(options){
	    options.buttons = options.buttons || this.buttons;
	    var view = Radio.request('buttons', 'view', options);
	    this.footer.show(view);
	  },

	  modalTitle: function(title){
	    //title = title || this.$('.modal-header h1').data('title');
	    this.$('.modal-header h1').html(title);
	  },

	  modalClassName: function(className){
	    if(className){
	      this.$('.modal-dialog').addClass(className);
	    }
	  },

	  getButtons: function(){
	    return this.getRegion('footer').currentView;
	  }

	});

/***/ },
/* 60 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var hbs = __webpack_require__(7);
	var Tmpl = __webpack_require__(62);
	var _ = __webpack_require__(5);
	var polyglot = __webpack_require__(10);

	module.exports = ItemView.extend({
	  template: hbs.compile(Tmpl),

	  initialize: function(options){
	    options = options || {};
	    var defaults = {
	      title: polyglot.t('messages.loading'),
	      close: polyglot.t('buttons.close')
	    };
	    this.data = _.defaults(options, defaults);
	  },

	  templateHelpers: function(){
	    this.data.iconPrefix = window.adminpage ? 'wc_pos-icon' : 'icon';
	    return this.data;
	  },

	  onUpdate: function(options){
	    _.extend(this.data, options);
	    this.render();
	  }
	});

/***/ },
/* 61 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var app = __webpack_require__(2);

	module.exports = app.prototype.ItemView = Mn.ItemView;

/***/ },
/* 62 */
/***/ function(module, exports) {

	module.exports = "<h4 class=\"{{namespace 'modal-title'}} modal-title\">\n  {{{title}}}\n</h4>\n<a class=\"{{namespace 'btn'}} close\" data-action=\"close\">\n  <i class=\"{{namespace 'icon-times'}}\" title=\"{{close}}\"></i>\n</a>"

/***/ },
/* 63 */
/***/ function(module, exports) {

	module.exports = "<div class=\"{{namespace 'modal-dialog'}} modal-dialog\">\n  <div class=\"{{namespace 'modal-content'}} modal-content\">\n    <div class=\"{{namespace 'modal-header'}} modal-header\"></div>\n    <div class=\"{{namespace 'modal-body'}} modal-body\"></div>\n    <div class=\"{{namespace 'modal-footer'}} modal-footer\"></div>\n  </div>\n</div>"

/***/ },
/* 64 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module, __webpack_require__(65)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module, require('./util'));
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod, global.Util);
	    global.modal = mod.exports;
	  }
	})(this, function (exports, module, _util) {
	  'use strict';

	  var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	  function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { 'default': obj }; }

	  function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	  var _Util = _interopRequireDefault(_util);

	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0): modal.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  var Modal = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Constants
	     * ------------------------------------------------------------------------
	     */

	    var NAME = 'modal';
	    var VERSION = '4.0.0';
	    var DATA_KEY = 'bs.modal';
	    var EVENT_KEY = '.' + DATA_KEY;
	    var DATA_API_KEY = '.data-api';
	    var JQUERY_NO_CONFLICT = $.fn[NAME];
	    var TRANSITION_DURATION = 300;
	    var BACKDROP_TRANSITION_DURATION = 150;

	    var Default = {
	      backdrop: true,
	      keyboard: true,
	      focus: true,
	      show: true
	    };

	    var DefaultType = {
	      backdrop: '(boolean|string)',
	      keyboard: 'boolean',
	      focus: 'boolean',
	      show: 'boolean'
	    };

	    var Event = {
	      HIDE: 'hide' + EVENT_KEY,
	      HIDDEN: 'hidden' + EVENT_KEY,
	      SHOW: 'show' + EVENT_KEY,
	      SHOWN: 'shown' + EVENT_KEY,
	      FOCUSIN: 'focusin' + EVENT_KEY,
	      RESIZE: 'resize' + EVENT_KEY,
	      CLICK_DISMISS: 'click.dismiss' + EVENT_KEY,
	      KEYDOWN_DISMISS: 'keydown.dismiss' + EVENT_KEY,
	      MOUSEUP_DISMISS: 'mouseup.dismiss' + EVENT_KEY,
	      MOUSEDOWN_DISMISS: 'mousedown.dismiss' + EVENT_KEY,
	      CLICK_DATA_API: 'click' + EVENT_KEY + DATA_API_KEY
	    };

	    var ClassName = {
	      SCROLLBAR_MEASURER: 'modal-scrollbar-measure',
	      BACKDROP: 'modal-backdrop',
	      OPEN: 'modal-open',
	      FADE: 'fade',
	      IN: 'in'
	    };

	    var Selector = {
	      DIALOG: '.modal-dialog',
	      DATA_TOGGLE: '[data-toggle="modal"]',
	      DATA_DISMISS: '[data-dismiss="modal"]',
	      FIXED_CONTENT: '.navbar-fixed-top, .navbar-fixed-bottom, .is-fixed'
	    };

	    /**
	     * ------------------------------------------------------------------------
	     * Class Definition
	     * ------------------------------------------------------------------------
	     */

	    var Modal = (function () {
	      function Modal(element, config) {
	        _classCallCheck(this, Modal);

	        this._config = this._getConfig(config);
	        this._element = element;
	        this._dialog = $(element).find(Selector.DIALOG)[0];
	        this._backdrop = null;
	        this._isShown = false;
	        this._isBodyOverflowing = false;
	        this._ignoreBackdropClick = false;
	        this._originalBodyPadding = 0;
	        this._scrollbarWidth = 0;
	      }

	      /**
	       * ------------------------------------------------------------------------
	       * Data Api implementation
	       * ------------------------------------------------------------------------
	       */

	      // getters

	      _createClass(Modal, [{
	        key: 'toggle',

	        // public

	        value: function toggle(relatedTarget) {
	          return this._isShown ? this.hide() : this.show(relatedTarget);
	        }
	      }, {
	        key: 'show',
	        value: function show(relatedTarget) {
	          var _this = this;

	          var showEvent = $.Event(Event.SHOW, {
	            relatedTarget: relatedTarget
	          });

	          $(this._element).trigger(showEvent);

	          if (this._isShown || showEvent.isDefaultPrevented()) {
	            return;
	          }

	          this._isShown = true;

	          this._checkScrollbar();
	          this._setScrollbar();

	          $(document.body).addClass(ClassName.OPEN);

	          this._setEscapeEvent();
	          this._setResizeEvent();

	          $(this._element).on(Event.CLICK_DISMISS, Selector.DATA_DISMISS, $.proxy(this.hide, this));

	          $(this._dialog).on(Event.MOUSEDOWN_DISMISS, function () {
	            $(_this._element).one(Event.MOUSEUP_DISMISS, function (event) {
	              if ($(event.target).is(_this._element)) {
	                that._ignoreBackdropClick = true;
	              }
	            });
	          });

	          this._showBackdrop($.proxy(this._showElement, this, relatedTarget));
	        }
	      }, {
	        key: 'hide',
	        value: function hide(event) {
	          if (event) {
	            event.preventDefault();
	          }

	          var hideEvent = $.Event(Event.HIDE);

	          $(this._element).trigger(hideEvent);

	          if (!this._isShown || hideEvent.isDefaultPrevented()) {
	            return;
	          }

	          this._isShown = false;

	          this._setEscapeEvent();
	          this._setResizeEvent();

	          $(document).off(Event.FOCUSIN);

	          $(this._element).removeClass(ClassName.IN);

	          $(this._element).off(Event.CLICK_DISMISS);
	          $(this._dialog).off(Event.MOUSEDOWN_DISMISS);

	          if (_Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.FADE)) {

	            $(this._element).one(_Util['default'].TRANSITION_END, $.proxy(this._hideModal, this)).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            this._hideModal();
	          }
	        }
	      }, {
	        key: 'dispose',
	        value: function dispose() {
	          $.removeData(this._element, DATA_KEY);

	          $(window).off(EVENT_KEY);
	          $(document).off(EVENT_KEY);
	          $(this._element).off(EVENT_KEY);
	          $(this._backdrop).off(EVENT_KEY);

	          this._config = null;
	          this._element = null;
	          this._dialog = null;
	          this._backdrop = null;
	          this._isShown = null;
	          this._isBodyOverflowing = null;
	          this._ignoreBackdropClick = null;
	          this._originalBodyPadding = null;
	          this._scrollbarWidth = null;
	        }

	        // private

	      }, {
	        key: '_getConfig',
	        value: function _getConfig(config) {
	          config = $.extend({}, Default, config);
	          _Util['default'].typeCheckConfig(NAME, config, DefaultType);
	          return config;
	        }
	      }, {
	        key: '_showElement',
	        value: function _showElement(relatedTarget) {
	          var _this2 = this;

	          var transition = _Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.FADE);

	          if (!this._element.parentNode || this._element.parentNode.nodeType !== Node.ELEMENT_NODE) {
	            // don't move modals dom position
	            document.body.appendChild(this._element);
	          }

	          this._element.style.display = 'block';
	          this._element.scrollTop = 0;

	          if (transition) {
	            _Util['default'].reflow(this._element);
	          }

	          $(this._element).addClass(ClassName.IN);

	          if (this._config.focus) {
	            this._enforceFocus();
	          }

	          var shownEvent = $.Event(Event.SHOWN, {
	            relatedTarget: relatedTarget
	          });

	          var transitionComplete = function transitionComplete() {
	            if (_this2._config.focus) {
	              _this2._element.focus();
	            }
	            $(_this2._element).trigger(shownEvent);
	          };

	          if (transition) {
	            $(this._dialog).one(_Util['default'].TRANSITION_END, transitionComplete).emulateTransitionEnd(TRANSITION_DURATION);
	          } else {
	            transitionComplete();
	          }
	        }
	      }, {
	        key: '_enforceFocus',
	        value: function _enforceFocus() {
	          var _this3 = this;

	          $(document).off(Event.FOCUSIN) // guard against infinite focus loop
	          .on(Event.FOCUSIN, function (event) {
	            if (_this3._element !== event.target && !$(_this3._element).has(event.target).length) {
	              _this3._element.focus();
	            }
	          });
	        }
	      }, {
	        key: '_setEscapeEvent',
	        value: function _setEscapeEvent() {
	          var _this4 = this;

	          if (this._isShown && this._config.keyboard) {
	            $(this._element).on(Event.KEYDOWN_DISMISS, function (event) {
	              if (event.which === 27) {
	                _this4.hide();
	              }
	            });
	          } else if (!this._isShown) {
	            $(this._element).off(Event.KEYDOWN_DISMISS);
	          }
	        }
	      }, {
	        key: '_setResizeEvent',
	        value: function _setResizeEvent() {
	          if (this._isShown) {
	            $(window).on(Event.RESIZE, $.proxy(this._handleUpdate, this));
	          } else {
	            $(window).off(Event.RESIZE);
	          }
	        }
	      }, {
	        key: '_hideModal',
	        value: function _hideModal() {
	          var _this5 = this;

	          this._element.style.display = 'none';
	          this._showBackdrop(function () {
	            $(document.body).removeClass(ClassName.OPEN);
	            _this5._resetAdjustments();
	            _this5._resetScrollbar();
	            $(_this5._element).trigger(Event.HIDDEN);
	          });
	        }
	      }, {
	        key: '_removeBackdrop',
	        value: function _removeBackdrop() {
	          if (this._backdrop) {
	            $(this._backdrop).remove();
	            this._backdrop = null;
	          }
	        }
	      }, {
	        key: '_showBackdrop',
	        value: function _showBackdrop(callback) {
	          var _this6 = this;

	          var animate = $(this._element).hasClass(ClassName.FADE) ? ClassName.FADE : '';

	          if (this._isShown && this._config.backdrop) {
	            var doAnimate = _Util['default'].supportsTransitionEnd() && animate;

	            this._backdrop = document.createElement('div');
	            this._backdrop.className = ClassName.BACKDROP;

	            if (animate) {
	              $(this._backdrop).addClass(animate);
	            }

	            $(this._backdrop).appendTo(document.body);

	            $(this._element).on(Event.CLICK_DISMISS, function (event) {
	              if (_this6._ignoreBackdropClick) {
	                _this6._ignoreBackdropClick = false;
	                return;
	              }
	              if (event.target !== event.currentTarget) {
	                return;
	              }
	              if (_this6._config.backdrop === 'static') {
	                _this6._element.focus();
	              } else {
	                _this6.hide();
	              }
	            });

	            if (doAnimate) {
	              _Util['default'].reflow(this._backdrop);
	            }

	            $(this._backdrop).addClass(ClassName.IN);

	            if (!callback) {
	              return;
	            }

	            if (!doAnimate) {
	              callback();
	              return;
	            }

	            $(this._backdrop).one(_Util['default'].TRANSITION_END, callback).emulateTransitionEnd(BACKDROP_TRANSITION_DURATION);
	          } else if (!this._isShown && this._backdrop) {
	            $(this._backdrop).removeClass(ClassName.IN);

	            var callbackRemove = function callbackRemove() {
	              _this6._removeBackdrop();
	              if (callback) {
	                callback();
	              }
	            };

	            if (_Util['default'].supportsTransitionEnd() && $(this._element).hasClass(ClassName.FADE)) {
	              $(this._backdrop).one(_Util['default'].TRANSITION_END, callbackRemove).emulateTransitionEnd(BACKDROP_TRANSITION_DURATION);
	            } else {
	              callbackRemove();
	            }
	          } else if (callback) {
	            callback();
	          }
	        }

	        // ----------------------------------------------------------------------
	        // the following methods are used to handle overflowing modals
	        // todo (fat): these should probably be refactored out of modal.js
	        // ----------------------------------------------------------------------

	      }, {
	        key: '_handleUpdate',
	        value: function _handleUpdate() {
	          this._adjustDialog();
	        }
	      }, {
	        key: '_adjustDialog',
	        value: function _adjustDialog() {
	          var isModalOverflowing = this._element.scrollHeight > document.documentElement.clientHeight;

	          if (!this._isBodyOverflowing && isModalOverflowing) {
	            this._element.style.paddingLeft = this._scrollbarWidth + 'px';
	          }

	          if (this._isBodyOverflowing && !isModalOverflowing) {
	            this._element.style.paddingRight = this._scrollbarWidth + 'px~';
	          }
	        }
	      }, {
	        key: '_resetAdjustments',
	        value: function _resetAdjustments() {
	          this._element.style.paddingLeft = '';
	          this._element.style.paddingRight = '';
	        }
	      }, {
	        key: '_checkScrollbar',
	        value: function _checkScrollbar() {
	          var fullWindowWidth = window.innerWidth;
	          if (!fullWindowWidth) {
	            // workaround for missing window.innerWidth in IE8
	            var documentElementRect = document.documentElement.getBoundingClientRect();
	            fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left);
	          }
	          this._isBodyOverflowing = document.body.clientWidth < fullWindowWidth;
	          this._scrollbarWidth = this._getScrollbarWidth();
	        }
	      }, {
	        key: '_setScrollbar',
	        value: function _setScrollbar() {
	          var bodyPadding = parseInt($(Selector.FIXED_CONTENT).css('padding-right') || 0, 10);

	          this._originalBodyPadding = document.body.style.paddingRight || '';

	          if (this._isBodyOverflowing) {
	            document.body.style.paddingRight = bodyPadding + this._scrollbarWidth + 'px';
	          }
	        }
	      }, {
	        key: '_resetScrollbar',
	        value: function _resetScrollbar() {
	          document.body.style.paddingRight = this._originalBodyPadding;
	        }
	      }, {
	        key: '_getScrollbarWidth',
	        value: function _getScrollbarWidth() {
	          // thx d.walsh
	          var scrollDiv = document.createElement('div');
	          scrollDiv.className = ClassName.SCROLLBAR_MEASURER;
	          document.body.appendChild(scrollDiv);
	          var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth;
	          document.body.removeChild(scrollDiv);
	          return scrollbarWidth;
	        }

	        // static

	      }], [{
	        key: '_jQueryInterface',
	        value: function _jQueryInterface(config, relatedTarget) {
	          return this.each(function () {
	            var data = $(this).data(DATA_KEY);
	            var _config = $.extend({}, Modal.Default, $(this).data(), typeof config === 'object' && config);

	            if (!data) {
	              data = new Modal(this, _config);
	              $(this).data(DATA_KEY, data);
	            }

	            if (typeof config === 'string') {
	              if (data[config] === undefined) {
	                throw new Error('No method named "' + config + '"');
	              }
	              data[config](relatedTarget);
	            } else if (_config.show) {
	              data.show(relatedTarget);
	            }
	          });
	        }
	      }, {
	        key: 'VERSION',
	        get: function get() {
	          return VERSION;
	        }
	      }, {
	        key: 'Default',
	        get: function get() {
	          return Default;
	        }
	      }]);

	      return Modal;
	    })();

	    $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function (event) {
	      var _this7 = this;

	      var target = undefined;
	      var selector = _Util['default'].getSelectorFromElement(this);

	      if (selector) {
	        target = $(selector)[0];
	      }

	      var config = $(target).data(DATA_KEY) ? 'toggle' : $.extend({}, $(target).data(), $(this).data());

	      if (this.tagName === 'A') {
	        event.preventDefault();
	      }

	      var $target = $(target).one(Event.SHOW, function (showEvent) {
	        if (showEvent.isDefaultPrevented()) {
	          // only register focus restorer if modal will actually get shown
	          return;
	        }

	        $target.one(Event.HIDDEN, function () {
	          if ($(_this7).is(':visible')) {
	            _this7.focus();
	          }
	        });
	      });

	      Modal._jQueryInterface.call($(target), config, this);
	    });

	    /**
	     * ------------------------------------------------------------------------
	     * jQuery
	     * ------------------------------------------------------------------------
	     */

	    $.fn[NAME] = Modal._jQueryInterface;
	    $.fn[NAME].Constructor = Modal;
	    $.fn[NAME].noConflict = function () {
	      $.fn[NAME] = JQUERY_NO_CONFLICT;
	      return Modal._jQueryInterface;
	    };

	    return Modal;
	  })(jQuery);

	  module.exports = Modal;
	});


/***/ },
/* 65 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;(function (global, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [exports, module], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports !== 'undefined' && typeof module !== 'undefined') {
	    factory(exports, module);
	  } else {
	    var mod = {
	      exports: {}
	    };
	    factory(mod.exports, mod);
	    global.util = mod.exports;
	  }
	})(this, function (exports, module) {
	  /**
	   * --------------------------------------------------------------------------
	   * Bootstrap (v4.0.0): util.js
	   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	   * --------------------------------------------------------------------------
	   */

	  'use strict';

	  var Util = (function ($) {

	    /**
	     * ------------------------------------------------------------------------
	     * Private TransitionEnd Helpers
	     * ------------------------------------------------------------------------
	     */

	    var transition = false;

	    var TransitionEndEvent = {
	      WebkitTransition: 'webkitTransitionEnd',
	      MozTransition: 'transitionend',
	      OTransition: 'oTransitionEnd otransitionend',
	      transition: 'transitionend'
	    };

	    // shoutout AngusCroll (https://goo.gl/pxwQGp)
	    function toType(obj) {
	      return ({}).toString.call(obj).match(/\s([a-zA-Z]+)/)[1].toLowerCase();
	    }

	    function isElement(obj) {
	      return (obj[0] || obj).nodeType;
	    }

	    function getSpecialTransitionEndEvent() {
	      return {
	        bindType: transition.end,
	        delegateType: transition.end,
	        handle: function handle(event) {
	          if ($(event.target).is(this)) {
	            return event.handleObj.handler.apply(this, arguments);
	          }
	        }
	      };
	    }

	    function transitionEndTest() {
	      if (window.QUnit) {
	        return false;
	      }

	      var el = document.createElement('bootstrap');

	      for (var _name in TransitionEndEvent) {
	        if (el.style[_name] !== undefined) {
	          return { end: TransitionEndEvent[_name] };
	        }
	      }

	      return false;
	    }

	    function transitionEndEmulator(duration) {
	      var _this = this;

	      var called = false;

	      $(this).one(Util.TRANSITION_END, function () {
	        called = true;
	      });

	      setTimeout(function () {
	        if (!called) {
	          Util.triggerTransitionEnd(_this);
	        }
	      }, duration);

	      return this;
	    }

	    function setTransitionEndSupport() {
	      transition = transitionEndTest();

	      $.fn.emulateTransitionEnd = transitionEndEmulator;

	      if (Util.supportsTransitionEnd()) {
	        $.event.special[Util.TRANSITION_END] = getSpecialTransitionEndEvent();
	      }
	    }

	    /**
	     * --------------------------------------------------------------------------
	     * Public Util Api
	     * --------------------------------------------------------------------------
	     */

	    var Util = {

	      TRANSITION_END: 'bsTransitionEnd',

	      getUID: function getUID(prefix) {
	        do {
	          prefix += ~ ~(Math.random() * 1000000);
	        } while (document.getElementById(prefix));
	        return prefix;
	      },

	      getSelectorFromElement: function getSelectorFromElement(element) {
	        var selector = element.getAttribute('data-target');

	        if (!selector) {
	          selector = element.getAttribute('href') || '';
	          selector = /^#[a-z]/i.test(selector) ? selector : null;
	        }

	        return selector;
	      },

	      reflow: function reflow(element) {
	        new Function('bs', 'return bs')(element.offsetHeight);
	      },

	      triggerTransitionEnd: function triggerTransitionEnd(element) {
	        $(element).trigger(transition.end);
	      },

	      supportsTransitionEnd: function supportsTransitionEnd() {
	        return Boolean(transition);
	      },

	      typeCheckConfig: function typeCheckConfig(componentName, config, configTypes) {
	        for (var property in configTypes) {
	          if (configTypes.hasOwnProperty(property)) {
	            var expectedTypes = configTypes[property];
	            var value = config[property];
	            var valueType = undefined;

	            if (value && isElement(value)) {
	              valueType = 'element';
	            } else {
	              valueType = toType(value);
	            }

	            if (!new RegExp(expectedTypes).test(valueType)) {
	              throw new Error(componentName.toUpperCase() + ': ' + ('Option "' + property + '" provided type "' + valueType + '" ') + ('but expected type "' + expectedTypes + '".'));
	            }
	          }
	        }
	      }
	    };

	    setTransitionEndSupport();

	    return Util;
	  })(jQuery);

	  module.exports = Util;
	});


/***/ },
/* 66 */
/***/ function(module, exports, __webpack_require__) {

	var View = __webpack_require__(61);
	var hbs = __webpack_require__(7);
	var Tmpl = __webpack_require__(67);

	module.exports = View.extend({
	  template: hbs.compile(Tmpl),

	  initialize: function(options){
	    options = options || {};
	    this.message = options.message;
	    this.raw = options.raw;

	    this.modal = {
	      className: options.className,
	      header: {
	        title: options.title
	      },
	      footer: {
	        buttons: [{
	          action: 'close'
	        }]
	      }
	    };
	  },

	  templateHelpers: function(){
	    var data = {};
	    data.message = this.message;
	    data.raw = this.raw;
	    return data;
	  },

	  ui: {
	    raw: '*[data-action="raw"]',
	    output: '.raw-output'
	  },

	  events: {
	    'click @ui.raw': 'toggleRaw'
	  },

	  toggleRaw: function(e){
	    e.preventDefault();
	    this.ui.output.toggle();
	  }

	});

/***/ },
/* 67 */
/***/ function(module, exports) {

	module.exports = "<p>\n  {{message}}\n  {{#if raw}}\n    <a href=\"#\" data-action=\"raw\"><i class=\"{{namespace 'icon-info-circle'}}\"></i></a>\n  {{/if}}\n<p>\n{{#if raw}}\n  <div class=\"raw-output\" style=\"display:none\">{{{raw}}}</div>\n{{/if}}"

/***/ },
/* 68 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var TabsView = __webpack_require__(69);
	var TabsCollection = __webpack_require__(73);
	//var _ = require('lodash');

	module.exports = Service.extend({
	  channelName: 'tabs',

	  initialize: function (){
	    this.channel.reply({
	      'view' : this.tabsView
	    }, this);
	  },

	  /**
	   * returns an instance of the tabs view
	   */
	  tabsView: function(options){
	    options = options || {};
	    return new TabsView({
	      collection: this.tabsCollection(options)
	    });
	  },

	  /**
	   *
	   */
	  tabsCollection: function(options){
	    return new TabsCollection(options.tabs);
	  }

	});

/***/ },
/* 69 */
/***/ function(module, exports, __webpack_require__) {

	var CollectionView = __webpack_require__(70);
	var Tab = __webpack_require__(71);

	var View = CollectionView.extend({
	  tagName: 'ul',
	  childView: Tab,
	  attributes: {
	    'class' : 'tabs',
	    'role'  : 'tablist'
	  },

	  setActive: function(id){
	    var model = this.collection.get(id);
	    model.set({active: true});
	  },

	  setLabel: function(options){
	    options = options || {};
	    var model = this.collection.get(options.tab);
	    model.set({label: options.label});
	  },

	  onShow: function(){
	    // last call for active tabs
	    this.collection.ensureActiveTab();
	  }
	});

	module.exports = View;

/***/ },
/* 70 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var app = __webpack_require__(2);

	module.exports = app.prototype.CollectionView = Mn.CollectionView.extend({
	  //// Marionette's default implementation ignores the index, always
	  //// appending the new view to the end. Let's be a little more clever.
	  //appendHtml: function(collectionView, itemView, index){
	  //  if (!index) {
	  //    collectionView.$el.prepend(itemView.el);
	  //  } else {
	  //    $(collectionView.$('li')[index - 1]).after(itemView.el);
	  //  }
	  //}
	});

/***/ },
/* 71 */
/***/ function(module, exports, __webpack_require__) {

	var hbs = __webpack_require__(7);
	var ItemView = __webpack_require__(61);
	var Tmpl = __webpack_require__(72);

	var View = ItemView.extend({
	  tagName: 'li',
	  template: hbs.compile(Tmpl),

	  className: function () {
	    if (this.model.get('active')) {
	      return 'active';
	    }
	  },

	  modelEvents: {
	    'change:active': 'toggleActive',
	    'change:label' : 'render' // why does this not auto render?!
	  },

	  toggleActive: function(){
	    this.$el.toggleClass('active', this.model.get('active'));
	  },

	  triggers: {
	    'click': 'tab:clicked',
	    'click *[data-action="remove"]': 'remove:tab'
	  },

	  onTabClicked: function () {
	    this.model.set({active: true});
	  },

	  onRemoveTab: function(){
	    this.model.collection.remove(this.model);
	  }

	});

	module.exports = View;

/***/ },
/* 72 */
/***/ function(module, exports) {

	module.exports = "{{#unless fixed}}\n<a href=\"#\" data-action=\"remove\">\n  <i class=\"icon icon-times-circle\"></i>\n</a>\n{{/unless}}\n{{{ label }}}"

/***/ },
/* 73 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(24);
	var Model = __webpack_require__(74);

	var TabsCollection = Collection.extend({
	  model: Model,

	  initialize: function(){
	    this.on({
	      'change:active' : this.onChangeActive,
	      'remove'        : this.ensureActiveTab
	    });
	  },

	  onChangeActive: function(model, active){
	    if(!active){ return; }
	    this.each(function(m) {
	      m.set({active: m === model});
	    });
	    this.trigger('active:tab', model);
	  },

	  ensureActiveTab: function() {
	    var activeTabs = this.where({'active': true});
	    if( this.length > 0 && activeTabs.length === 0 ) {
	      this.at(0).set({active: true});
	    }
	  }

	});

	module.exports = TabsCollection;

/***/ },
/* 74 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(31);

	var TabModel = Model.extend({
	  defaults: {
	    id: '',
	    label: 'Tab',
	    active: false,
	    fixed: true
	  }
	});

	module.exports = TabModel;

/***/ },
/* 75 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var View = __webpack_require__(76);

	module.exports = Service.extend({

	  channelName: 'buttons',

	  initialize: function(){

	    this.channel.reply({
	      'view' : this.view
	    }, this);

	  },

	  view: function(options){
	    var view = new View(options);
	    return view;
	  }

	});

/***/ },
/* 76 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var hbs = __webpack_require__(7);
	var _ = __webpack_require__(5);
	var tmpl = __webpack_require__(77);
	var polyglot = __webpack_require__(10);
	var ButtonsBehavior = __webpack_require__(78);

	module.exports = ItemView.extend({

	  viewOptions: ['buttons'],

	  buttons: [{
	    action: 'save',
	    className: 'btn-primary'
	  }],

	  template: hbs.compile(tmpl),

	  initialize: function(options){
	    this.mergeOptions(options, this.viewOptions);
	  },

	  templateHelpers: function(){
	    _.each(this.buttons, function(button){
	      var type = button.type || 'button';
	      button[type] = true;
	      button.label = button.label || polyglot.t('buttons.' + button.action);
	    });
	    return {
	      buttons: this.buttons
	    };
	  },

	  behaviors: {
	    Buttons: {
	      behaviorClass: ButtonsBehavior
	    }
	  }

	});

/***/ },
/* 77 */
/***/ function(module, exports) {

	module.exports = "{{#each buttons}}\n\n  {{#if this.button}}\n    <button class=\"btn {{this.className}}\"\n            {{#if this.action}}data-action=\"{{this.action}}\"{{/if}}\n      {{#if this.toggle}}data-toggle=\"{{this.toggle}}\"{{/if}}\n      {{#if this.loading}}data-loading=\"{{this.loadingText}}\"{{/if}}\n      {{#if this.icon}}data-icon=\"{{this.icon}}\"{{/if}}\n      {{#if this.disabled}}disabled{{/if}}\n      >\n      {{this.label}}\n    </button>\n  {{/if}}\n\n  {{#if this.link}}\n    <a href=\"#\" class=\"btn {{this.className}}\"\n       {{#if this.action}}data-action=\"{{this.action}}\"{{/if}}\n      {{#if this.toggle}}data-toggle=\"{{this.toggle}}\"{{/if}}\n      {{#if this.loading}}data-loading=\"{{this.loadingText}}\"{{/if}}\n      {{#if this.icon}}data-icon=\"{{this.icon}}\"{{/if}}\n      >\n      {{this.label}}\n    </a>\n  {{/if}}\n\n  {{#if this.input}}\n    <input type=\"button\" class=\"btn {{this.className}}\" value=\"{{this.label}}\"\n           {{#if this.action}}data-action=\"{{this.action}}\"{{/if}}\n      {{#if this.toggle}}data-toggle=\"{{this.toggle}}\"{{/if}}\n      {{#if this.loading}}data-loading=\"{{this.loadingText}}\"{{/if}}\n      >\n  {{/if}}\n\n  {{#if this.message}}\n    <p class=\"message {{this.className}}\"></p>\n  {{/if}}\n\n{{/each}}"

/***/ },
/* 78 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var App = __webpack_require__(2);
	var $ = __webpack_require__(6);
	var polyglot = __webpack_require__(10);
	var d = 'disabled';

	var Buttons = Behavior.extend({
	  loadingText: polyglot.t('messages.loading'),

	  ui: {
	    btns    : '.btn',
	    action  : '[data-action]',
	    toggle  : '[data-toggle]',
	    message : '.message'
	  },

	  events: {
	    'click @ui.action': 'action',
	    'click @ui.toggle': 'toggle',
	    'state @ui.btns'  : 'setState'
	  },

	  namespace: function( str ){
	    // test for wp-admin
	    if(window.adminpage){
	      str = 'wc_pos-' + str;
	    }
	    return str;
	  },

	  action: function(e){
	    e.preventDefault();
	    var action = $(e.target).data('action');
	    this.view.trigger('action:' + action, $(e.target), this.view, action );
	  },

	  toggle: function(e){
	    e.preventDefault();
	    this.enable().disable($(e.target));
	    var toggle = $(e.target).data('toggle');
	    this.view.trigger('toggle:' + toggle, $(e.target), this.view, toggle);
	  },

	  enable: function(btn){
	    if(btn){
	      btn.removeClass(d).prop(d, false);
	    } else {
	      this.ui.btns.each(function(){
	        $(this).removeClass(d).prop(d, false);
	      });
	    }
	    return this;
	  },

	  disable: function(btn){
	    if(btn){
	      btn.addClass(d).prop(d, true);
	    } else {
	      this.ui.btns.each(function(){
	        $(this).addClass(d).prop(d, true);
	      });
	    }
	    return this;
	  },

	  setState: function(e, state, message){
	    var btn = $(e.target),
	        prop = state === 'loading' ? 'disable' : 'enable';
	    this[prop]();
	    this.updateText(btn);
	    if( btn.is('input') ){
	      this.updateInput(btn, state);
	    } else {
	      this.updateIcon(btn, state);
	    }
	    if(message !== undefined){
	      this.updateMessage(message, state);
	    }
	  },

	  updateText: function(btn){
	    if(btn.data('loading') === undefined){ return; }
	    var val  = btn.is('input') ? 'val' : 'html';
	    var text = btn[val]();
	    var loadingText = btn.data('loading') || this.loadingText;
	    btn.data('loading', text);
	    btn[val](loadingText);
	  },

	  updateIcon: function(btn, state){
	    if(btn.data('icon') === undefined){ return; }
	    var pos = btn.data('icon') || 'prepend';
	    var icon = state !== 'reset' ? this.icon(state) : '';
	    btn.children('i').remove();
	    btn[pos](icon);
	  },

	  icon: function(state){
	    return '<i class="' + this.namespace( 'icon-' + state ) + '"></i>';
	  },

	  updateInput: function(btn, state){
	    btn.removeClass('loading success error');
	    if(state !== 'reset'){
	      btn.addClass(state);
	    }
	  },

	  updateMessage: function(message, state){
	    if(message === null){
	      message = polyglot.t('messages.' + state);
	    }
	    if(!state){
	      state = message;
	      message = polyglot.t('messages.' + message);
	    }
	    if(state === 'reset'){
	      message = '';
	    }
	    this.ui.message
	      .removeClass('loading success error')
	      .addClass( this.namespace( 'text-' + state ) )
	      .html(message);
	  },

	  onMessage: function(message, state){
	    this.updateMessage(message, state);
	  },

	  onDisableButtons: function(){
	    this.disable();
	  },

	  onEnableButtons: function(){
	    this.enable();
	  }

	});

	module.exports = Buttons;
	App.prototype.set('Behaviors.Buttons', Buttons);

/***/ },
/* 79 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var app = __webpack_require__(2);

	module.exports = app.prototype.Behavior = Mn.Behavior;

/***/ },
/* 80 */,
/* 81 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(16);
	var Mn = __webpack_require__(3);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var Route = __webpack_require__(82);
	var app = __webpack_require__(2);

	module.exports = app.prototype.Router = Mn.AppRouter.extend({
	  constructor: function() {
	    this.channel = bb.Radio.channel('router');
	    this.on('all', this._onRouterEvent);
	    this.listenTo(bb.history, 'route', this._onHistoryRoute);
	    Mn.AppRouter.apply(this, arguments);
	  },

	  _onRouterEvent: function(name) {
	    var args = _.toArray(arguments).slice(1);
	    this.channel.trigger.apply(this.channel, [name, this].concat(args));
	  },

	  _onHistoryRoute: function(router) {
	    if (this === router) {
	      this.active = true;
	    } else {
	      this.active = false;
	      this._currentRoute = undefined;
	    }
	  },

	  execute: function(callback, args) {
	    var self = this;

	    if (!this.active) {
	      this.triggerMethod.apply(this, ['before:enter'].concat(args));
	    }

	    this.triggerMethod.apply(this, ['before:route'].concat(args));

	    $.when(this._execute(callback, args)).then(function() {
	      if (!self.active) {
	        self.triggerMethod.apply(self, ['enter'].concat(args));
	      }

	      self.triggerMethod.apply(self, ['route'].concat(args));
	    });
	  },

	  _execute: function(callback, args) {
	    var route = callback.apply(this, args);
	    this._currentRoute = route;

	    if (route instanceof Route) {
	      route.router = this;
	      return route.enter(args);
	    }
	  },

	  triggerMethod: Mn.triggerMethod
	});

/***/ },
/* 82 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(16);
	var Mn = __webpack_require__(3);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var app = __webpack_require__(2);
	var LoadingService = __webpack_require__(83);
	var Radio = __webpack_require__(4);
	var globalChannel = Radio.channel('global');

	module.exports = app.prototype.Route = Mn.Object.extend({
	  constructor: function() {
	    this.initialize.apply(this, arguments);
	  },

	  _triggerMethod: function(name, args) {
	    if (this.router) {
	      this.router.triggerMethod.apply(
	        this.router,
	        [name + ':route'].concat(args)
	      );
	    }
	    this.triggerMethod.apply(this, [name].concat(args));
	  },

	  enter: function(args) {
	    var self = this;
	    this.transitioning = true;
	    this._triggerMethod('before:enter', args);
	    this._triggerMethod('before:fetch', args);

	    _.defer(function() {
	      if (self.transitioning) {
	        self.loading = new LoadingService({
	          container: self.container
	        });
	      }
	    });

	    return $.when(this.fetch.call(this, args)).then(function() {
	      self._triggerMethod('fetch', args);
	      self._triggerMethod('before:render', args);
	    }).then(function() {
	      self.transitioning = false;
	      return self.render.apply(self, args);
	    }).then(function() {
	      self._triggerMethod('render', args);
	      self._triggerMethod('enter', args);
	    }).fail(function() {
	      self._triggerMethod('error', args);
	    });
	  },

	  navigate: function() {
	    bb.history.navigate.apply(bb.history, arguments);
	  },

	  fetch  : function() {},
	  render : function() {},

	  setTabLabel: function(options){
	    globalChannel.trigger('tab:label', options);
	  }

	});

/***/ },
/* 83 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var View = __webpack_require__(84);
	var _ = __webpack_require__(5);
	var debug = __webpack_require__(13)('loading');

	module.exports = Service.extend({
	  channelName: 'loading',

	  initialize: function (options) {
	    _.defaults(options, {
	      type    : 'spinner',
	      message : ''
	    });

	    if(options.container && this[options.type]){
	      this[options.type](options);
	    } else {
	      debug('invalid loading options', options);
	    }
	  },

	  spinner: function(options){
	    var view = new View({
	      message: options.message
	    });
	    options.container.show(view);
	  },

	  opacity: function(options){
	    options.container.currentView.$el.css({
	      'opacity': 0.5
	    });
	  }

	});

/***/ },
/* 84 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var _ = __webpack_require__(5);
	var App = __webpack_require__(2);

	var View = ItemView.extend({
	  className: 'loading',
	  iconPrefix: 'icon-',

	  initialize: function () {
	    this.on('update:message', this.render);
	    this.timeout = setTimeout(_.bind(this.fail, this), 60000);
	    // test for wp-admin
	    if(window.adminpage){
	      this.iconPrefix = 'wc_pos-icon-';
	    }
	  },

	  render: function () {
	    var message = '';
	    if (!_.isEmpty(this.options.message)) {
	      message = '<p>' + this.options.message + '</p>';
	    }
	    this.$el.html('<p>' + this.icon() + '</p>' + message);
	    return this;
	  },

	  onBeforeDestroy: function () {
	    clearTimeout(this.timeout);
	  },

	  /**
	   * Loading fail. Will automatically get called after 60s
	   * @param message
	   */
	  fail: function (message) {
	    if (message) {
	      this.options.message = message;
	    } else {
	      this.options.message = 'Script Error';
	    }
	    this.render();
	    this.$('i').removeClass('icon-spinner').addClass('icon-fail');
	  },

	  icon: function(){
	    return '<i class="' +
	      this.iconPrefix + 'spinner ' +
	      this.iconPrefix + 'lg"></i>';
	  }

	});

	module.exports = View;
	App.prototype.set('Components.Loading.View', View);

/***/ },
/* 85 */,
/* 86 */,
/* 87 */,
/* 88 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var bb = __webpack_require__(16);
	var app = __webpack_require__(2);
	__webpack_require__(89);
	__webpack_require__(90);
	__webpack_require__(91);

	module.exports = app.prototype.FormView = ItemView.extend({

	  constructor: function() {
	    return ItemView.prototype.constructor.apply(this, arguments);
	  },

	  bindings: {},

	  render: function(){
	    // Invoke original render function
	    var args = Array.prototype.slice.apply(arguments);
	    var result = ItemView.prototype.render.apply(this, args);

	    // Apply validation
	    bb.Validation.bind(this, {
	      model: this.model,
	      valid: function(view, attr) {
	        view
	          .$('input[name="' + attr + '"]')
	          .removeClass('form-control-error')
	          .parent()
	          .removeClass('has-error');
	      },
	      invalid: function(view, attr) {
	        view
	          .$('input[name="' + attr + '"]')
	          .addClass('form-control-error')
	          .parent()
	          .addClass('has-error');
	      }
	    });

	    // Apply stickit
	    this.stickit();

	    // Return render result
	    return result;
	  },

	  remove: function() {
	    // Remove the validation binding
	    bb.Validation.unbind(this);
	    return ItemView.prototype.remove.apply(this, arguments);
	  }

	});

/***/ },
/* 89 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;// Backbone.Stickit v0.9.2, MIT Licensed
	// Copyright (c) 2012-2015 The New York Times, CMS Group, Matthew DeLambo <delambo@gmail.com>

	(function (factory) {

	  // Set up Stickit appropriately for the environment. Start with AMD.
	  if (true)
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(5), __webpack_require__(16), exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));

	  // Next for Node.js or CommonJS.
	  else if (typeof exports === 'object')
	    factory(require('underscore'), require('backbone'), exports);

	  // Finally, as a browser global.
	  else
	    factory(_, Backbone, {});

	}(function (_, Backbone, Stickit) {

	  // Stickit Namespace
	  // --------------------------

	  // Export onto Backbone object
	  Backbone.Stickit = Stickit;

	  Stickit._handlers = [];

	  Stickit.addHandler = function(handlers) {
	    // Fill-in default values.
	    handlers = _.map(_.flatten([handlers]), function(handler) {
	      return _.defaults({}, handler, {
	        updateModel: true,
	        updateView: true,
	        updateMethod: 'text'
	      });
	    });
	    this._handlers = this._handlers.concat(handlers);
	  };

	  // Backbone.View Mixins
	  // --------------------

	  Stickit.ViewMixin = {

	    // Collection of model event bindings.
	    //   [{model,event,fn,config}, ...]
	    _modelBindings: null,

	    // Unbind the model and event bindings from `this._modelBindings` and
	    // `this.$el`. If the optional `model` parameter is defined, then only
	    // delete bindings for the given `model` and its corresponding view events.
	    unstickit: function(model, bindingSelector) {

	      // Support passing a bindings hash in place of bindingSelector.
	      if (_.isObject(bindingSelector)) {
	        _.each(bindingSelector, function(v, selector) {
	          this.unstickit(model, selector);
	        }, this);
	        return;
	      }

	      var models = [], destroyFns = [];
	      this._modelBindings = _.reject(this._modelBindings, function(binding) {
	        if (model && binding.model !== model) return;
	        if (bindingSelector && binding.config.selector != bindingSelector) return;

	        binding.model.off(binding.event, binding.fn);
	        destroyFns.push(binding.config._destroy);
	        models.push(binding.model);
	        return true;
	      });

	      // Trigger an event for each model that was unbound.
	      _.invoke(_.uniq(models), 'trigger', 'stickit:unstuck', this.cid);

	      // Call `_destroy` on a unique list of the binding callbacks.
	      _.each(_.uniq(destroyFns), function(fn) { fn.call(this); }, this);

	      this.$el.off('.stickit' + (model ? '.' + model.cid : ''), bindingSelector);
	    },

	    // Initilize Stickit bindings for the view. Subsequent binding additions
	    // can either call `stickit` with the new bindings, or add them directly
	    // with `addBinding`. Both arguments to `stickit` are optional.
	    stickit: function(optionalModel, optionalBindingsConfig) {
	      var model = optionalModel || this.model,
	          bindings = optionalBindingsConfig || _.result(this, "bindings") || {};

	      this._modelBindings || (this._modelBindings = []);

	      // Add bindings in bulk using `addBinding`.
	      this.addBinding(model, bindings);

	      // Wrap `view.remove` to unbind stickit model and dom events.
	      var remove = this.remove;
	      if (!remove.stickitWrapped) {
	        this.remove = function() {
	          var ret = this;
	          this.unstickit();
	          if (remove) ret = remove.apply(this, arguments);
	          return ret;
	        };
	      }
	      this.remove.stickitWrapped = true;
	      return this;
	    },

	    // Add a single Stickit binding or a hash of bindings to the model. If
	    // `optionalModel` is ommitted, will default to the view's `model` property.
	    addBinding: function(optionalModel, selector, binding) {
	      var model = optionalModel || this.model,
	          namespace = '.stickit.' + model.cid;

	      binding = binding || {};

	      // Support jQuery-style {key: val} event maps.
	      if (_.isObject(selector)) {
	        var bindings = selector;
	        _.each(bindings, function(val, key) {
	          this.addBinding(model, key, val);
	        }, this);
	        return;
	      }

	      // Special case the ':el' selector to use the view's this.$el.
	      var $el = selector === ':el' ? this.$el : this.$(selector);

	      // Clear any previous matching bindings.
	      this.unstickit(model, selector);

	      // Fail fast if the selector didn't match an element.
	      if (!$el.length) return;

	      // Allow shorthand setting of model attributes - `'selector':'observe'`.
	      if (_.isString(binding)) binding = {observe: binding};

	      // Handle case where `observe` is in the form of a function.
	      if (_.isFunction(binding.observe)) binding.observe = binding.observe.call(this);

	      // Find all matching Stickit handlers that could apply to this element
	      // and store in a config object.
	      var config = getConfiguration($el, binding);

	      // The attribute we're observing in our config.
	      var modelAttr = config.observe;

	      // Store needed properties for later.
	      config.selector = selector;
	      config.view = this;

	      // Create the model set options with a unique `bindId` so that we
	      // can avoid double-binding in the `change:attribute` event handler.
	      var bindId = config.bindId = _.uniqueId();

	      // Add a reference to the view for handlers of stickitChange events
	      var options = _.extend({stickitChange: config}, config.setOptions);

	      // Add a `_destroy` callback to the configuration, in case `destroy`
	      // is a named function and we need a unique function when unsticking.
	      config._destroy = function() {
	        applyViewFn.call(this, config.destroy, $el, model, config);
	      };

	      initializeAttributes($el, config, model, modelAttr);
	      initializeVisible($el, config, model, modelAttr);
	      initializeClasses($el, config, model, modelAttr);

	      if (modelAttr) {
	        // Setup one-way (input element -> model) bindings.
	        _.each(config.events, function(type) {
	          var eventName = type + namespace;
	          var listener = function(event) {
	            var val = applyViewFn.call(this, config.getVal, $el, event, config, slice.call(arguments, 1));

	            // Don't update the model if false is returned from the `updateModel` configuration.
	            var currentVal = evaluateBoolean(config.updateModel, val, event, config);
	            if (currentVal) setAttr(model, modelAttr, val, options, config);
	          };
	          var sel = selector === ':el'? '' : selector;
	          this.$el.on(eventName, sel, _.bind(listener, this));
	        }, this);

	        // Setup a `change:modelAttr` observer to keep the view element in sync.
	        // `modelAttr` may be an array of attributes or a single string value.
	        _.each(_.flatten([modelAttr]), function(attr) {
	          observeModelEvent(model, 'change:' + attr, config, function(m, val, options) {
	            var changeId = options && options.stickitChange && options.stickitChange.bindId;
	            if (changeId !== bindId) {
	              var currentVal = getAttr(model, modelAttr, config);
	              updateViewBindEl($el, config, currentVal, model);
	            }
	          });
	        });

	        var currentVal = getAttr(model, modelAttr, config);
	        updateViewBindEl($el, config, currentVal, model, true);
	      }

	      // After each binding is setup, call the `initialize` callback.
	      applyViewFn.call(this, config.initialize, $el, model, config);
	    }
	  };

	  _.extend(Backbone.View.prototype, Stickit.ViewMixin);

	  // Helpers
	  // -------

	  var slice = [].slice;

	  // Evaluates the given `path` (in object/dot-notation) relative to the given
	  // `obj`. If the path is null/undefined, then the given `obj` is returned.
	  var evaluatePath = function(obj, path) {
	    var parts = (path || '').split('.');
	    var result = _.reduce(parts, function(memo, i) { return memo[i]; }, obj);
	    return result == null ? obj : result;
	  };

	  // If the given `fn` is a string, then view[fn] is called, otherwise it is
	  // a function that should be executed.
	  var applyViewFn = function(fn) {
	    fn = _.isString(fn) ? evaluatePath(this, fn) : fn;
	    if (fn) return (fn).apply(this, slice.call(arguments, 1));
	  };

	  // Given a function, string (view function reference), or a boolean
	  // value, returns the truthy result. Any other types evaluate as false.
	  // The first argument must be `reference` and the last must be `config`, but
	  // middle arguments can be variadic.
	  var evaluateBoolean = function(reference, val, config) {
	    if (_.isBoolean(reference)) {
	      return reference;
	    } else if (_.isFunction(reference) || _.isString(reference)) {
	      var view = _.last(arguments).view;
	      return applyViewFn.apply(view, arguments);
	    }
	    return false;
	  };

	  // Setup a model event binding with the given function, and track the event
	  // in the view's _modelBindings.
	  var observeModelEvent = function(model, event, config, fn) {
	    var view = config.view;
	    model.on(event, fn, view);
	    view._modelBindings.push({model:model, event:event, fn:fn, config:config});
	  };

	  // Prepares the given `val`ue and sets it into the `model`.
	  var setAttr = function(model, attr, val, options, config) {
	    var value = {}, view = config.view;
	    if (config.onSet) {
	      val = applyViewFn.call(view, config.onSet, val, config);
	    }

	    if (config.set) {
	      applyViewFn.call(view, config.set, attr, val, options, config);
	    } else {
	      value[attr] = val;
	      // If `observe` is defined as an array and `onSet` returned
	      // an array, then map attributes to their values.
	      if (_.isArray(attr) && _.isArray(val)) {
	        value = _.reduce(attr, function(memo, attribute, index) {
	          memo[attribute] = _.has(val, index) ? val[index] : null;
	          return memo;
	        }, {});
	      }
	      model.set(value, options);
	    }
	  };

	  // Returns the given `attr`'s value from the `model`, escaping and
	  // formatting if necessary. If `attr` is an array, then an array of
	  // respective values will be returned.
	  var getAttr = function(model, attr, config) {
	    var view = config.view;
	    var retrieveVal = function(field) {
	      return model[config.escape ? 'escape' : 'get'](field);
	    };
	    var sanitizeVal = function(val) {
	      return val == null ? '' : val;
	    };
	    var val = _.isArray(attr) ? _.map(attr, retrieveVal) : retrieveVal(attr);
	    if (config.onGet) val = applyViewFn.call(view, config.onGet, val, config);
	    return _.isArray(val) ? _.map(val, sanitizeVal) : sanitizeVal(val);
	  };

	  // Find handlers in `Backbone.Stickit._handlers` with selectors that match
	  // `$el` and generate a configuration by mixing them in the order that they
	  // were found with the given `binding`.
	  var getConfiguration = Stickit.getConfiguration = function($el, binding) {
	    var handlers = [{
	      updateModel: false,
	      updateMethod: 'text',
	      update: function($el, val, m, opts) { if ($el[opts.updateMethod]) $el[opts.updateMethod](val); },
	      getVal: function($el, e, opts) { return $el[opts.updateMethod](); }
	    }];
	    handlers = handlers.concat(_.filter(Stickit._handlers, function(handler) {
	      return $el.is(handler.selector);
	    }));
	    handlers.push(binding);

	    // Merge handlers into a single config object. Last props in wins.
	    var config = _.extend.apply(_, handlers);

	    // `updateView` is defaulted to false for configutrations with
	    // `visible`; otherwise, `updateView` is defaulted to true.
	    if (!_.has(config, 'updateView')) config.updateView = !config.visible;
	    return config;
	  };

	  // Setup the attributes configuration - a list that maps an attribute or
	  // property `name`, to an `observe`d model attribute, using an optional
	  // `onGet` formatter.
	  //
	  //     attributes: [{
	  //       name: 'attributeOrPropertyName',
	  //       observe: 'modelAttrName'
	  //       onGet: function(modelAttrVal, modelAttrName) { ... }
	  //     }, ...]
	  //
	  var initializeAttributes = function($el, config, model, modelAttr) {
	    var props = ['autofocus', 'autoplay', 'async', 'checked', 'controls',
	      'defer', 'disabled', 'hidden', 'indeterminate', 'loop', 'multiple',
	      'open', 'readonly', 'required', 'scoped', 'selected'];

	    var view = config.view;

	    _.each(config.attributes || [], function(attrConfig) {
	      attrConfig = _.clone(attrConfig);
	      attrConfig.view = view;

	      var lastClass = '';
	      var observed = attrConfig.observe || (attrConfig.observe = modelAttr);
	      var updateAttr = function() {
	        var updateType = _.contains(props, attrConfig.name) ? 'prop' : 'attr',
	            val = getAttr(model, observed, attrConfig);

	        // If it is a class then we need to remove the last value and add the new.
	        if (attrConfig.name === 'class') {
	          $el.removeClass(lastClass).addClass(val);
	          lastClass = val;
	        } else {
	          $el[updateType](attrConfig.name, val);
	        }
	      };

	      _.each(_.flatten([observed]), function(attr) {
	        observeModelEvent(model, 'change:' + attr, config, updateAttr);
	      });

	      // Initialize the matched element's state.
	      updateAttr();
	    });
	  };

	  var initializeClasses = function($el, config, model, modelAttr) {
	    _.each(config.classes || [], function(classConfig, name) {
	      if (_.isString(classConfig)) classConfig = {observe: classConfig};
	      classConfig.view = config.view;

	      var observed = classConfig.observe;
	      var updateClass = function() {
	        var val = getAttr(model, observed, classConfig);
	        $el.toggleClass(name, !!val);
	      };

	      _.each(_.flatten([observed]), function(attr) {
	        observeModelEvent(model, 'change:' + attr, config, updateClass);
	      });
	      updateClass();
	    });
	  };

	  // If `visible` is configured, then the view element will be shown/hidden
	  // based on the truthiness of the modelattr's value or the result of the
	  // given callback. If a `visibleFn` is also supplied, then that callback
	  // will be executed to manually handle showing/hiding the view element.
	  //
	  //     observe: 'isRight',
	  //     visible: true, // or function(val, options) {}
	  //     visibleFn: function($el, isVisible, options) {} // optional handler
	  //
	  var initializeVisible = function($el, config, model, modelAttr) {
	    if (config.visible == null) return;
	    var view = config.view;

	    var visibleCb = function() {
	      var visible = config.visible,
	          visibleFn = config.visibleFn,
	          val = getAttr(model, modelAttr, config),
	          isVisible = !!val;

	      // If `visible` is a function then it should return a boolean result to show/hide.
	      if (_.isFunction(visible) || _.isString(visible)) {
	        isVisible = !!applyViewFn.call(view, visible, val, config);
	      }

	      // Either use the custom `visibleFn`, if provided, or execute the standard show/hide.
	      if (visibleFn) {
	        applyViewFn.call(view, visibleFn, $el, isVisible, config);
	      } else {
	        $el.toggle(isVisible);
	      }
	    };

	    _.each(_.flatten([modelAttr]), function(attr) {
	      observeModelEvent(model, 'change:' + attr, config, visibleCb);
	    });

	    visibleCb();
	  };

	  // Update the value of `$el` using the given configuration and trigger the
	  // `afterUpdate` callback. This action may be blocked by `config.updateView`.
	  //
	  //     update: function($el, val, model, options) {},  // handler for updating
	  //     updateView: true, // defaults to true
	  //     afterUpdate: function($el, val, options) {} // optional callback
	  //
	  var updateViewBindEl = function($el, config, val, model, isInitializing) {
	    var view = config.view;
	    if (!evaluateBoolean(config.updateView, val, config)) return;
	    applyViewFn.call(view, config.update, $el, val, model, config);
	    if (!isInitializing) applyViewFn.call(view, config.afterUpdate, $el, val, config);
	  };

	  // Default Handlers
	  // ----------------

	  Stickit.addHandler([{
	    selector: '[contenteditable]',
	    updateMethod: 'html',
	    events: ['input', 'change']
	  }, {
	    selector: 'input',
	    events: ['propertychange', 'input', 'change'],
	    update: function($el, val) { $el.val(val); },
	    getVal: function($el) {
	      return $el.val();
	    }
	  }, {
	    selector: 'textarea',
	    events: ['propertychange', 'input', 'change'],
	    update: function($el, val) { $el.val(val); },
	    getVal: function($el) { return $el.val(); }
	  }, {
	    selector: 'input[type="radio"]',
	    events: ['change'],
	    update: function($el, val) {
	      $el.filter('[value="'+val+'"]').prop('checked', true);
	    },
	    getVal: function($el) {
	      return $el.filter(':checked').val();
	    }
	  }, {
	    selector: 'input[type="checkbox"]',
	    events: ['change'],
	    update: function($el, val, model, options) {
	      if ($el.length > 1) {
	        // There are multiple checkboxes so we need to go through them and check
	        // any that have value attributes that match what's in the array of `val`s.
	        val || (val = []);
	        $el.each(function(i, el) {
	          var checkbox = Backbone.$(el);
	          var checked = _.contains(val, checkbox.val());
	          checkbox.prop('checked', checked);
	        });
	      } else {
	        var checked = _.isBoolean(val) ? val : val === $el.val();
	        $el.prop('checked', checked);
	      }
	    },
	    getVal: function($el) {
	      var val;
	      if ($el.length > 1) {
	        val = _.reduce($el, function(memo, el) {
	          var checkbox = Backbone.$(el);
	          if (checkbox.prop('checked')) memo.push(checkbox.val());
	          return memo;
	        }, []);
	      } else {
	        val = $el.prop('checked');
	        // If the checkbox has a value attribute defined, then
	        // use that value. Most browsers use "on" as a default.
	        var boxval = $el.val();
	        if (boxval !== 'on' && boxval != null) {
	          val = val ? $el.val() : null;
	        }
	      }
	      return val;
	    }
	  }, {
	    selector: 'select',
	    events: ['change'],
	    update: function($el, val, model, options) {
	      var optList,
	        selectConfig = options.selectOptions,
	        list = selectConfig && selectConfig.collection || undefined,
	        isMultiple = $el.prop('multiple');

	      // If there are no `selectOptions` then we assume that the `<select>`
	      // is pre-rendered and that we need to generate the collection.
	      if (!selectConfig) {
	        selectConfig = {};
	        var getList = function($el) {
	          return $el.map(function(index, option) {
	            // Retrieve the text and value of the option, preferring "stickit-bind-val"
	            // data attribute over value property.
	            var dataVal = Backbone.$(option).data('stickit-bind-val');
	            return {
	              value: dataVal !== undefined ? dataVal : option.value,
	              label: option.text
	            };
	          }).get();
	        };
	        if ($el.find('optgroup').length) {
	          list = {opt_labels:[]};
	          // Search for options without optgroup
	          if ($el.find('> option').length) {
	            list.opt_labels.push(undefined);
	            _.each($el.find('> option'), function(el) {
	              list[undefined] = getList(Backbone.$(el));
	            });
	          }
	          _.each($el.find('optgroup'), function(el) {
	            var label = Backbone.$(el).attr('label');
	            list.opt_labels.push(label);
	            list[label] = getList(Backbone.$(el).find('option'));
	          });
	        } else {
	          list = getList($el.find('option'));
	        }
	      }

	      // Fill in default label and path values.
	      selectConfig.valuePath = selectConfig.valuePath || 'value';
	      selectConfig.labelPath = selectConfig.labelPath || 'label';
	      selectConfig.disabledPath = selectConfig.disabledPath || 'disabled';

	      var addSelectOptions = function(optList, $el, fieldVal) {
	        _.each(optList, function(obj) {
	          var option = Backbone.$('<option/>'), optionVal = obj;

	          var fillOption = function(text, val, disabled) {
	            option.text(text);
	            optionVal = val;
	            // Save the option value as data so that we can reference it later.
	            option.data('stickit-bind-val', optionVal);
	            if (!_.isArray(optionVal) && !_.isObject(optionVal)) option.val(optionVal);

	            if (disabled === true) option.prop('disabled', 'disabled');
	          };

	          var text, val, disabled;
	          if (obj === '__default__') {
	            text = fieldVal.label,
	            val = fieldVal.value,
	            disabled = fieldVal.disabled;
	          } else {
	            text = evaluatePath(obj, selectConfig.labelPath),
	            val = evaluatePath(obj, selectConfig.valuePath),
	            disabled = evaluatePath(obj, selectConfig.disabledPath);
	          }
	          fillOption(text, val, disabled);

	          // Determine if this option is selected.
	          var isSelected = function() {
	            if (!isMultiple && optionVal != null && fieldVal != null && optionVal === fieldVal) {
	              return true;
	            } else if (_.isObject(fieldVal) && _.isEqual(optionVal, fieldVal)) {
	              return true;
	            }
	            return false;
	          };

	          if (isSelected()) {
	            option.prop('selected', true);
	          } else if (isMultiple && _.isArray(fieldVal)) {
	            _.each(fieldVal, function(val) {
	              if (_.isObject(val)) val = evaluatePath(val, selectConfig.valuePath);
	              if (val === optionVal || (_.isObject(val) && _.isEqual(optionVal, val)))
	                option.prop('selected', true);
	            });
	          }

	          $el.append(option);
	        });
	      };

	      $el.find('*').remove();

	      // The `list` configuration is a function that returns the options list or a string
	      // which represents the path to the list relative to `window` or the view/`this`.
	      if (_.isString(list)) {
	        var context = window;
	        if (list.indexOf('this.') === 0) context = this;
	        list = list.replace(/^[a-z]*\.(.+)$/, '$1');
	        optList = evaluatePath(context, list);
	      } else if (_.isFunction(list)) {
	        optList = applyViewFn.call(this, list, $el, options);
	      } else {
	        optList = list;
	      }

	      // Support Backbone.Collection and deserialize.
	      if (optList instanceof Backbone.Collection) {
	        var collection = optList;
	        var refreshSelectOptions = function() {
	          var currentVal = getAttr(model, options.observe, options);
	          applyViewFn.call(this, options.update, $el, currentVal, model, options);
	        };
	        // We need to call this function after unstickit and after an update so we don't end up
	        // with multiple listeners doing the same thing
	        var removeCollectionListeners = function() {
	          collection.off('add remove reset sort', refreshSelectOptions);
	        };
	        var removeAllListeners = function() {
	          removeCollectionListeners();
	          collection.off('stickit:selectRefresh');
	          model.off('stickit:selectRefresh');
	        };
	        // Remove previously set event listeners by triggering a custom event
	        collection.trigger('stickit:selectRefresh');
	        collection.once('stickit:selectRefresh', removeCollectionListeners, this);

	        // Listen to the collection and trigger an update of the select options
	        collection.on('add remove reset sort', refreshSelectOptions, this);

	        // Remove the previous model event listener
	        model.trigger('stickit:selectRefresh');
	        model.once('stickit:selectRefresh', function() {
	          model.off('stickit:unstuck', removeAllListeners);
	        });
	        // Remove collection event listeners once this binding is unstuck
	        model.once('stickit:unstuck', removeAllListeners, this);
	        optList = optList.toJSON();
	      }

	      if (selectConfig.defaultOption) {
	        var option = _.isFunction(selectConfig.defaultOption) ?
	          selectConfig.defaultOption.call(this, $el, options) :
	          selectConfig.defaultOption;
	        addSelectOptions(["__default__"], $el, option);
	      }

	      if (_.isArray(optList)) {
	        addSelectOptions(optList, $el, val);
	      } else if (optList.opt_labels) {
	        // To define a select with optgroups, format selectOptions.collection as an object
	        // with an 'opt_labels' property, as in the following:
	        //
	        //     {
	        //       'opt_labels': ['Looney Tunes', 'Three Stooges'],
	        //       'Looney Tunes': [{id: 1, name: 'Bugs Bunny'}, {id: 2, name: 'Donald Duck'}],
	        //       'Three Stooges': [{id: 3, name : 'moe'}, {id: 4, name : 'larry'}, {id: 5, name : 'curly'}]
	        //     }
	        //
	        _.each(optList.opt_labels, function(label) {
	          var $group = Backbone.$('<optgroup/>').attr('label', label);
	          addSelectOptions(optList[label], $group, val);
	          $el.append($group);
	        });
	        // With no 'opt_labels' parameter, the object is assumed to be a simple value-label map.
	        // Pass a selectOptions.comparator to override the default order of alphabetical by label.
	      } else {
	        var opts = [], opt;
	        for (var i in optList) {
	          opt = {};
	          opt[selectConfig.valuePath] = i;
	          opt[selectConfig.labelPath] = optList[i];
	          opts.push(opt);
	        }
	        opts = _.sortBy(opts, selectConfig.comparator || selectConfig.labelPath);
	        addSelectOptions(opts, $el, val);
	      }
	    },
	    getVal: function($el) {
	      var selected = $el.find('option:selected');

	      if ($el.prop('multiple')) {
	        return _.map(selected, function(el) {
	          return Backbone.$(el).data('stickit-bind-val');
	        });
	      } else {
	        return selected.data('stickit-bind-val');
	      }
	    }
	  }]);

	  return Stickit;

	}));


/***/ },
/* 90 */
/***/ function(module, exports, __webpack_require__) {

	// Backbone.Validation v0.11.5
	//
	// Copyright (c) 2011-2015 Thomas Pedersen
	// Distributed under MIT License
	//
	// Documentation and full license available at:
	// http://thedersen.com/projects/backbone-validation
	(function (factory) {
	  if (true) {
	    module.exports = factory(__webpack_require__(16), __webpack_require__(5));
	  } else if (typeof define === 'function' && define.amd) {
	    define(['backbone', 'underscore'], factory);
	  }
	}(function (Backbone, _) {
	  Backbone.Validation = (function(_){
	    'use strict';
	  
	    // Default options
	    // ---------------
	  
	    var defaultOptions = {
	      forceUpdate: false,
	      selector: 'name',
	      labelFormatter: 'sentenceCase',
	      valid: Function.prototype,
	      invalid: Function.prototype
	    };
	  
	  
	    // Helper functions
	    // ----------------
	  
	    // Formatting functions used for formatting error messages
	    var formatFunctions = {
	      // Uses the configured label formatter to format the attribute name
	      // to make it more readable for the user
	      formatLabel: function(attrName, model) {
	        return defaultLabelFormatters[defaultOptions.labelFormatter](attrName, model);
	      },
	  
	      // Replaces nummeric placeholders like {0} in a string with arguments
	      // passed to the function
	      format: function() {
	        var args = Array.prototype.slice.call(arguments),
	            text = args.shift();
	        return text.replace(/\{(\d+)\}/g, function(match, number) {
	          return typeof args[number] !== 'undefined' ? args[number] : match;
	        });
	      }
	    };
	  
	    // Flattens an object
	    // eg:
	    //
	    //     var o = {
	    //       owner: {
	    //         name: 'Backbone',
	    //         address: {
	    //           street: 'Street',
	    //           zip: 1234
	    //         }
	    //       }
	    //     };
	    //
	    // becomes:
	    //
	    //     var o = {
	    //       'owner': {
	    //         name: 'Backbone',
	    //         address: {
	    //           street: 'Street',
	    //           zip: 1234
	    //         }
	    //       },
	    //       'owner.name': 'Backbone',
	    //       'owner.address': {
	    //         street: 'Street',
	    //         zip: 1234
	    //       },
	    //       'owner.address.street': 'Street',
	    //       'owner.address.zip': 1234
	    //     };
	    // This may seem redundant, but it allows for maximum flexibility
	    // in validation rules.
	    var flatten = function (obj, into, prefix) {
	      into = into || {};
	      prefix = prefix || '';
	  
	      _.each(obj, function(val, key) {
	        if(obj.hasOwnProperty(key)) {
	          if (!!val && _.isArray(val)) {
	            _.forEach(val, function(v, k) {
	              flatten(v, into, prefix + key + '.' + k + '.');
	              into[prefix + key + '.' + k] = v;
	            });
	          } else if (!!val && typeof val === 'object' && val.constructor === Object) {
	            flatten(val, into, prefix + key + '.');
	          }
	  
	          // Register the current level object as well
	          into[prefix + key] = val;
	        }
	      });
	  
	      return into;
	    };
	  
	    // Validation
	    // ----------
	  
	    var Validation = (function(){
	  
	      // Returns an object with undefined properties for all
	      // attributes on the model that has defined one or more
	      // validation rules.
	      var getValidatedAttrs = function(model, attrs) {
	        attrs = attrs || _.keys(_.result(model, 'validation') || {});
	        return _.reduce(attrs, function(memo, key) {
	          memo[key] = void 0;
	          return memo;
	        }, {});
	      };
	  
	      // Returns an array with attributes passed through options
	      var getOptionsAttrs = function(options, view) {
	        var attrs = options.attributes;
	        if (_.isFunction(attrs)) {
	          attrs = attrs(view);
	        } else if (_.isString(attrs) && (_.isFunction(defaultAttributeLoaders[attrs]))) {
	          attrs = defaultAttributeLoaders[attrs](view);
	        }
	        if (_.isArray(attrs)) {
	          return attrs;
	        }
	      };
	  
	  
	      // Looks on the model for validations for a specified
	      // attribute. Returns an array of any validators defined,
	      // or an empty array if none is defined.
	      var getValidators = function(model, attr) {
	        var attrValidationSet = model.validation ? _.result(model, 'validation')[attr] || {} : {};
	  
	        // If the validator is a function or a string, wrap it in a function validator
	        if (_.isFunction(attrValidationSet) || _.isString(attrValidationSet)) {
	          attrValidationSet = {
	            fn: attrValidationSet
	          };
	        }
	  
	        // Stick the validator object into an array
	        if(!_.isArray(attrValidationSet)) {
	          attrValidationSet = [attrValidationSet];
	        }
	  
	        // Reduces the array of validators into a new array with objects
	        // with a validation method to call, the value to validate against
	        // and the specified error message, if any
	        return _.reduce(attrValidationSet, function(memo, attrValidation) {
	          _.each(_.without(_.keys(attrValidation), 'msg'), function(validator) {
	            memo.push({
	              fn: defaultValidators[validator],
	              val: attrValidation[validator],
	              msg: attrValidation.msg
	            });
	          });
	          return memo;
	        }, []);
	      };
	  
	      // Validates an attribute against all validators defined
	      // for that attribute. If one or more errors are found,
	      // the first error message is returned.
	      // If the attribute is valid, an empty string is returned.
	      var validateAttr = function(model, attr, value, computed) {
	        // Reduces the array of validators to an error message by
	        // applying all the validators and returning the first error
	        // message, if any.
	        return _.reduce(getValidators(model, attr), function(memo, validator){
	          // Pass the format functions plus the default
	          // validators as the context to the validator
	          var ctx = _.extend({}, formatFunctions, defaultValidators),
	              result = validator.fn.call(ctx, value, attr, validator.val, model, computed);
	  
	          if(result === false || memo === false) {
	            return false;
	          }
	          if (result && !memo) {
	            return _.result(validator, 'msg') || result;
	          }
	          return memo;
	        }, '');
	      };
	  
	      // Loops through the model's attributes and validates the specified attrs.
	      // Returns and object containing names of invalid attributes
	      // as well as error messages.
	      var validateModel = function(model, attrs, validatedAttrs) {
	        var error,
	            invalidAttrs = {},
	            isValid = true,
	            computed = _.clone(attrs);
	  
	        _.each(validatedAttrs, function(val, attr) {
	          error = validateAttr(model, attr, val, computed);
	          if (error) {
	            invalidAttrs[attr] = error;
	            isValid = false;
	          }
	        });
	  
	        return {
	          invalidAttrs: invalidAttrs,
	          isValid: isValid
	        };
	      };
	  
	      // Contains the methods that are mixed in on the model when binding
	      var mixin = function(view, options) {
	        return {
	  
	          // Check whether or not a value, or a hash of values
	          // passes validation without updating the model
	          preValidate: function(attr, value) {
	            var self = this,
	                result = {},
	                error;
	  
	            if(_.isObject(attr)){
	              _.each(attr, function(value, key) {
	                error = self.preValidate(key, value);
	                if(error){
	                  result[key] = error;
	                }
	              });
	  
	              return _.isEmpty(result) ? undefined : result;
	            }
	            else {
	              return validateAttr(this, attr, value, _.extend({}, this.attributes));
	            }
	          },
	  
	          // Check to see if an attribute, an array of attributes or the
	          // entire model is valid. Passing true will force a validation
	          // of the model.
	          isValid: function(option) {
	            var flattened, attrs, error, invalidAttrs;
	  
	            option = option || getOptionsAttrs(options, view);
	  
	            if(_.isString(option)){
	              attrs = [option];
	            } else if(_.isArray(option)) {
	              attrs = option;
	            }
	            if (attrs) {
	              flattened = flatten(this.attributes);
	              //Loop through all associated views
	              _.each(this.associatedViews, function(view) {
	                _.each(attrs, function (attr) {
	                  error = validateAttr(this, attr, flattened[attr], _.extend({}, this.attributes));
	                  if (error) {
	                    options.invalid(view, attr, error, options.selector);
	                    invalidAttrs = invalidAttrs || {};
	                    invalidAttrs[attr] = error;
	                  } else {
	                    options.valid(view, attr, options.selector);
	                  }
	                }, this);
	              }, this);
	            }
	  
	            if(option === true) {
	              invalidAttrs = this.validate();
	            }
	            if (invalidAttrs) {
	              this.trigger('invalid', this, invalidAttrs, {validationError: invalidAttrs});
	            }
	            return attrs ? !invalidAttrs : this.validation ? this._isValid : true;
	          },
	  
	          // This is called by Backbone when it needs to perform validation.
	          // You can call it manually without any parameters to validate the
	          // entire model.
	          validate: function(attrs, setOptions){
	            var model = this,
	                validateAll = !attrs,
	                opt = _.extend({}, options, setOptions),
	                validatedAttrs = getValidatedAttrs(model, getOptionsAttrs(options, view)),
	                allAttrs = _.extend({}, validatedAttrs, model.attributes, attrs),
	                flattened = flatten(allAttrs),
	                changedAttrs = attrs ? flatten(attrs) : flattened,
	                result = validateModel(model, allAttrs, _.pick(flattened, _.keys(validatedAttrs)));
	  
	            model._isValid = result.isValid;
	  
	            //After validation is performed, loop through all associated views
	            _.each(model.associatedViews, function(view){
	  
	              // After validation is performed, loop through all validated and changed attributes
	              // and call the valid and invalid callbacks so the view is updated.
	              _.each(validatedAttrs, function(val, attr){
	                  var invalid = result.invalidAttrs.hasOwnProperty(attr),
	                    changed = changedAttrs.hasOwnProperty(attr);
	  
	                  if(!invalid){
	                    opt.valid(view, attr, opt.selector);
	                  }
	                  if(invalid && (changed || validateAll)){
	                    opt.invalid(view, attr, result.invalidAttrs[attr], opt.selector);
	                  }
	              });
	            });
	  
	            // Trigger validated events.
	            // Need to defer this so the model is actually updated before
	            // the event is triggered.
	            _.defer(function() {
	              model.trigger('validated', model._isValid, model, result.invalidAttrs);
	              model.trigger('validated:' + (model._isValid ? 'valid' : 'invalid'), model, result.invalidAttrs);
	            });
	  
	            // Return any error messages to Backbone, unless the forceUpdate flag is set.
	            // Then we do not return anything and fools Backbone to believe the validation was
	            // a success. That way Backbone will update the model regardless.
	            if (!opt.forceUpdate && _.intersection(_.keys(result.invalidAttrs), _.keys(changedAttrs)).length > 0) {
	              return result.invalidAttrs;
	            }
	          }
	        };
	      };
	  
	      // Helper to mix in validation on a model. Stores the view in the associated views array.
	      var bindModel = function(view, model, options) {
	        if (model.associatedViews) {
	          model.associatedViews.push(view);
	        } else {
	          model.associatedViews = [view];
	        }
	        _.extend(model, mixin(view, options));
	      };
	  
	      // Removes view from associated views of the model or the methods
	      // added to a model if no view or single view provided
	      var unbindModel = function(model, view) {
	        if (view && model.associatedViews && model.associatedViews.length > 1){
	          model.associatedViews = _.without(model.associatedViews, view);
	        } else {
	          delete model.validate;
	          delete model.preValidate;
	          delete model.isValid;
	          delete model.associatedViews;
	        }
	      };
	  
	      // Mix in validation on a model whenever a model is
	      // added to a collection
	      var collectionAdd = function(model) {
	        bindModel(this.view, model, this.options);
	      };
	  
	      // Remove validation from a model whenever a model is
	      // removed from a collection
	      var collectionRemove = function(model) {
	        unbindModel(model);
	      };
	  
	      // Returns the public methods on Backbone.Validation
	      return {
	  
	        // Current version of the library
	        version: '0.11.3',
	  
	        // Called to configure the default options
	        configure: function(options) {
	          _.extend(defaultOptions, options);
	        },
	  
	        // Hooks up validation on a view with a model
	        // or collection
	        bind: function(view, options) {
	          options = _.extend({}, defaultOptions, defaultCallbacks, options);
	  
	          var model = options.model || view.model,
	              collection = options.collection || view.collection;
	  
	          if(typeof model === 'undefined' && typeof collection === 'undefined'){
	            throw 'Before you execute the binding your view must have a model or a collection.\n' +
	                  'See http://thedersen.com/projects/backbone-validation/#using-form-model-validation for more information.';
	          }
	  
	          if(model) {
	            bindModel(view, model, options);
	          }
	          else if(collection) {
	            collection.each(function(model){
	              bindModel(view, model, options);
	            });
	            collection.bind('add', collectionAdd, {view: view, options: options});
	            collection.bind('remove', collectionRemove);
	          }
	        },
	  
	        // Removes validation from a view with a model
	        // or collection
	        unbind: function(view, options) {
	          options = _.extend({}, options);
	          var model = options.model || view.model,
	              collection = options.collection || view.collection;
	  
	          if(model) {
	            unbindModel(model, view);
	          }
	          else if(collection) {
	            collection.each(function(model){
	              unbindModel(model, view);
	            });
	            collection.unbind('add', collectionAdd);
	            collection.unbind('remove', collectionRemove);
	          }
	        },
	  
	        // Used to extend the Backbone.Model.prototype
	        // with validation
	        mixin: mixin(null, defaultOptions)
	      };
	    }());
	  
	  
	    // Callbacks
	    // ---------
	  
	    var defaultCallbacks = Validation.callbacks = {
	  
	      // Gets called when a previously invalid field in the
	      // view becomes valid. Removes any error message.
	      // Should be overridden with custom functionality.
	      valid: function(view, attr, selector) {
	        view.$('[' + selector + '~="' + attr + '"]')
	            .removeClass('invalid')
	            .removeAttr('data-error');
	      },
	  
	      // Gets called when a field in the view becomes invalid.
	      // Adds a error message.
	      // Should be overridden with custom functionality.
	      invalid: function(view, attr, error, selector) {
	        view.$('[' + selector + '~="' + attr + '"]')
	            .addClass('invalid')
	            .attr('data-error', error);
	      }
	    };
	  
	  
	    // Patterns
	    // --------
	  
	    var defaultPatterns = Validation.patterns = {
	      // Matches any digit(s) (i.e. 0-9)
	      digits: /^\d+$/,
	  
	      // Matches any number (e.g. 100.000)
	      number: /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/,
	  
	      // Matches a valid email address (e.g. mail@example.com)
	      email: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,
	  
	      // Mathes any valid url (e.g. http://www.xample.com)
	      url: /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i
	    };
	  
	  
	    // Error messages
	    // --------------
	  
	    // Error message for the build in validators.
	    // {x} gets swapped out with arguments form the validator.
	    var defaultMessages = Validation.messages = {
	      required: '{0} is required',
	      acceptance: '{0} must be accepted',
	      min: '{0} must be greater than or equal to {1}',
	      max: '{0} must be less than or equal to {1}',
	      range: '{0} must be between {1} and {2}',
	      length: '{0} must be {1} characters',
	      minLength: '{0} must be at least {1} characters',
	      maxLength: '{0} must be at most {1} characters',
	      rangeLength: '{0} must be between {1} and {2} characters',
	      oneOf: '{0} must be one of: {1}',
	      equalTo: '{0} must be the same as {1}',
	      digits: '{0} must only contain digits',
	      number: '{0} must be a number',
	      email: '{0} must be a valid email',
	      url: '{0} must be a valid url',
	      inlinePattern: '{0} is invalid'
	    };
	  
	    // Label formatters
	    // ----------------
	  
	    // Label formatters are used to convert the attribute name
	    // to a more human friendly label when using the built in
	    // error messages.
	    // Configure which one to use with a call to
	    //
	    //     Backbone.Validation.configure({
	    //       labelFormatter: 'label'
	    //     });
	    var defaultLabelFormatters = Validation.labelFormatters = {
	  
	      // Returns the attribute name with applying any formatting
	      none: function(attrName) {
	        return attrName;
	      },
	  
	      // Converts attributeName or attribute_name to Attribute name
	      sentenceCase: function(attrName) {
	        return attrName.replace(/(?:^\w|[A-Z]|\b\w)/g, function(match, index) {
	          return index === 0 ? match.toUpperCase() : ' ' + match.toLowerCase();
	        }).replace(/_/g, ' ');
	      },
	  
	      // Looks for a label configured on the model and returns it
	      //
	      //      var Model = Backbone.Model.extend({
	      //        validation: {
	      //          someAttribute: {
	      //            required: true
	      //          }
	      //        },
	      //
	      //        labels: {
	      //          someAttribute: 'Custom label'
	      //        }
	      //      });
	      label: function(attrName, model) {
	        return (model.labels && model.labels[attrName]) || defaultLabelFormatters.sentenceCase(attrName, model);
	      }
	    };
	  
	    // AttributeLoaders
	  
	    var defaultAttributeLoaders = Validation.attributeLoaders = {
	      inputNames: function (view) {
	        var attrs = [];
	        if (view) {
	          view.$('form [name]').each(function () {
	            if (/^(?:input|select|textarea)$/i.test(this.nodeName) && this.name &&
	              this.type !== 'submit' && attrs.indexOf(this.name) === -1) {
	              attrs.push(this.name);
	            }
	          });
	        }
	        return attrs;
	      }
	    };
	  
	  
	    // Built in validators
	    // -------------------
	  
	    var defaultValidators = Validation.validators = (function(){
	      // Use native trim when defined
	      var trim = String.prototype.trim ?
	        function(text) {
	          return text === null ? '' : String.prototype.trim.call(text);
	        } :
	        function(text) {
	          var trimLeft = /^\s+/,
	              trimRight = /\s+$/;
	  
	          return text === null ? '' : text.toString().replace(trimLeft, '').replace(trimRight, '');
	        };
	  
	      // Determines whether or not a value is a number
	      var isNumber = function(value){
	        return _.isNumber(value) || (_.isString(value) && value.match(defaultPatterns.number));
	      };
	  
	      // Determines whether or not a value is empty
	      var hasValue = function(value) {
	        return !(_.isNull(value) || _.isUndefined(value) || (_.isString(value) && trim(value) === '') || (_.isArray(value) && _.isEmpty(value)));
	      };
	  
	      return {
	        // Function validator
	        // Lets you implement a custom function used for validation
	        fn: function(value, attr, fn, model, computed) {
	          if(_.isString(fn)){
	            fn = model[fn];
	          }
	          return fn.call(model, value, attr, computed);
	        },
	  
	        // Required validator
	        // Validates if the attribute is required or not
	        // This can be specified as either a boolean value or a function that returns a boolean value
	        required: function(value, attr, required, model, computed) {
	          var isRequired = _.isFunction(required) ? required.call(model, value, attr, computed) : required;
	          if(!isRequired && !hasValue(value)) {
	            return false; // overrides all other validators
	          }
	          if (isRequired && !hasValue(value)) {
	            return this.format(defaultMessages.required, this.formatLabel(attr, model));
	          }
	        },
	  
	        // Acceptance validator
	        // Validates that something has to be accepted, e.g. terms of use
	        // `true` or 'true' are valid
	        acceptance: function(value, attr, accept, model) {
	          if(value !== 'true' && (!_.isBoolean(value) || value === false)) {
	            return this.format(defaultMessages.acceptance, this.formatLabel(attr, model));
	          }
	        },
	  
	        // Min validator
	        // Validates that the value has to be a number and equal to or greater than
	        // the min value specified
	        min: function(value, attr, minValue, model) {
	          if (!isNumber(value) || value < minValue) {
	            return this.format(defaultMessages.min, this.formatLabel(attr, model), minValue);
	          }
	        },
	  
	        // Max validator
	        // Validates that the value has to be a number and equal to or less than
	        // the max value specified
	        max: function(value, attr, maxValue, model) {
	          if (!isNumber(value) || value > maxValue) {
	            return this.format(defaultMessages.max, this.formatLabel(attr, model), maxValue);
	          }
	        },
	  
	        // Range validator
	        // Validates that the value has to be a number and equal to or between
	        // the two numbers specified
	        range: function(value, attr, range, model) {
	          if(!isNumber(value) || value < range[0] || value > range[1]) {
	            return this.format(defaultMessages.range, this.formatLabel(attr, model), range[0], range[1]);
	          }
	        },
	  
	        // Length validator
	        // Validates that the value has to be a string with length equal to
	        // the length value specified
	        length: function(value, attr, length, model) {
	          if (!_.isString(value) || value.length !== length) {
	            return this.format(defaultMessages.length, this.formatLabel(attr, model), length);
	          }
	        },
	  
	        // Min length validator
	        // Validates that the value has to be a string with length equal to or greater than
	        // the min length value specified
	        minLength: function(value, attr, minLength, model) {
	          if (!_.isString(value) || value.length < minLength) {
	            return this.format(defaultMessages.minLength, this.formatLabel(attr, model), minLength);
	          }
	        },
	  
	        // Max length validator
	        // Validates that the value has to be a string with length equal to or less than
	        // the max length value specified
	        maxLength: function(value, attr, maxLength, model) {
	          if (!_.isString(value) || value.length > maxLength) {
	            return this.format(defaultMessages.maxLength, this.formatLabel(attr, model), maxLength);
	          }
	        },
	  
	        // Range length validator
	        // Validates that the value has to be a string and equal to or between
	        // the two numbers specified
	        rangeLength: function(value, attr, range, model) {
	          if (!_.isString(value) || value.length < range[0] || value.length > range[1]) {
	            return this.format(defaultMessages.rangeLength, this.formatLabel(attr, model), range[0], range[1]);
	          }
	        },
	  
	        // One of validator
	        // Validates that the value has to be equal to one of the elements in
	        // the specified array. Case sensitive matching
	        oneOf: function(value, attr, values, model) {
	          if(!_.include(values, value)){
	            return this.format(defaultMessages.oneOf, this.formatLabel(attr, model), values.join(', '));
	          }
	        },
	  
	        // Equal to validator
	        // Validates that the value has to be equal to the value of the attribute
	        // with the name specified
	        equalTo: function(value, attr, equalTo, model, computed) {
	          if(value !== computed[equalTo]) {
	            return this.format(defaultMessages.equalTo, this.formatLabel(attr, model), this.formatLabel(equalTo, model));
	          }
	        },
	  
	        // Pattern validator
	        // Validates that the value has to match the pattern specified.
	        // Can be a regular expression or the name of one of the built in patterns
	        pattern: function(value, attr, pattern, model) {
	          if (!hasValue(value) || !value.toString().match(defaultPatterns[pattern] || pattern)) {
	            return this.format(defaultMessages[pattern] || defaultMessages.inlinePattern, this.formatLabel(attr, model), pattern);
	          }
	        }
	      };
	    }());
	  
	    // Set the correct context for all validators
	    // when used from within a method validator
	    _.each(defaultValidators, function(validator, key){
	      defaultValidators[key] = _.bind(defaultValidators[key], _.extend({}, formatFunctions, defaultValidators));
	    });
	  
	    return Validation;
	  }(_));
	  return Backbone.Validation;
	}));

/***/ },
/* 91 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(16);
	var _ = __webpack_require__(5);

	/**
	 * AutoGrow
	 */
	bb.Stickit.addHandler({
	  selector: '.autogrow',
	  afterUpdate: function($el){
	    $el.trigger('input');
	  }
	});

	/**
	 * Select2
	 */
	bb.Stickit.addHandler({
	  selector: 'select.select2',
	  initialize: function($el, model, opt){
	    $el.trigger('stickit:init', opt.observe); // on-the-fly select options
	    var options = _.get( opt, ['view', 'select2', opt.observe ], {} );
	    $el.select2( options );
	  },
	  getVal: function($el){
	    /**
	     * below is the default select getVal method
	     * it relies on data-stickit-bind-val attr
	     */

	    //var selected = $el.find('option:selected');
	    //
	    //if ($el.prop('multiple')) {
	    //  return _.map(selected, function(el) {
	    //    return Backbone.$(el).data('stickit-bind-val');
	    //  });
	    //} else {
	    //  return selected.data('stickit-bind-val');
	    //}

	    return $el.val();
	  }
	});

	/**
	 * Multiple selects with Select2
	 * ... bit of a hack here, setting an array only registers a change
	 * ie: if last element removed no change is registered
	 */
	bb.Stickit.addHandler({
	  selector: 'select[multiple].select2',
	  onSet: function(val, opts){
	    if(_.isArray(val)){
	      this.model.unset(opts.observe, {silent:true});
	    }
	    return val;
	  }
	});

/***/ },
/* 92 */,
/* 93 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var App = __webpack_require__(2);
	var _ = __webpack_require__(5);
	var $ = __webpack_require__(6);
	var Drop = __webpack_require__(94);
	var App = __webpack_require__(2);
	var namespace = App.prototype.namespace('tooltip');

	var _Drop = Drop.createContext({
	  classPrefix: namespace
	});

	var defaults = {
	  position: 'top center',
	  openOn: 'hover',
	  classes: namespace + '-theme-arrows',
	  constrainToWindow: true,
	  constrainToScrollParent: false,
	  remove: true
	};

	var TooltipBehavior = Behavior.extend({

	  _initialized: [],

	  initialize: function(options){
	    this.options = _.extend({}, defaults, options);

	    // define ui
	    this.ui = {
	      tooltip: '*[data-toggle="' + namespace + '"]'
	    };
	  },

	  events: {
	    'mouseenter @ui.tooltip': 'onHover'
	  },

	  onHover: function(e){
	    if(this._initialized.indexOf(e.target) !== -1) {
	      return;
	    }

	    // drop instance
	    var options = _.extend({}, this.options, {
	      target  : e.target,
	      content : $(e.target).attr('title')
	    });
	    var drop = new _Drop(options);
	    this._initialized.push(e.target);

	    // remove the title attribute to prevent browser hover
	    $(e.target).removeAttr('title');

	    drop.open();
	  }

	});

	module.exports = TooltipBehavior;
	App.prototype.set('Behaviors.Tooltip', TooltipBehavior);

/***/ },
/* 94 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*! tether-drop 1.3.1 */

	(function(root, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(95)], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports === 'object') {
	    module.exports = factory(require('tether'));
	  } else {
	    root.Drop = factory(root.Tether);
	  }
	}(this, function(Tether) {

	/* global Tether */

	'use strict';

	var _bind = Function.prototype.bind;

	var _slicedToArray = (function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i['return']) _i['return'](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError('Invalid attempt to destructure non-iterable instance'); } }; })();

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	var _get = function get(_x2, _x3, _x4) { var _again = true; _function: while (_again) { var object = _x2, property = _x3, receiver = _x4; desc = parent = getter = undefined; _again = false; if (object === null) object = Function.prototype; var desc = Object.getOwnPropertyDescriptor(object, property); if (desc === undefined) { var parent = Object.getPrototypeOf(object); if (parent === null) { return undefined; } else { _x2 = parent; _x3 = property; _x4 = receiver; _again = true; continue _function; } } else if ('value' in desc) { return desc.value; } else { var getter = desc.get; if (getter === undefined) { return undefined; } return getter.call(receiver); } } };

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	function _inherits(subClass, superClass) { if (typeof superClass !== 'function' && superClass !== null) { throw new TypeError('Super expression must either be null or a function, not ' + typeof superClass); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, enumerable: false, writable: true, configurable: true } }); if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass; }

	var _Tether$Utils = Tether.Utils;
	var extend = _Tether$Utils.extend;
	var addClass = _Tether$Utils.addClass;
	var removeClass = _Tether$Utils.removeClass;
	var hasClass = _Tether$Utils.hasClass;
	var Evented = _Tether$Utils.Evented;

	function sortAttach(str) {
	  var _str$split = str.split(' ');

	  var _str$split2 = _slicedToArray(_str$split, 2);

	  var first = _str$split2[0];
	  var second = _str$split2[1];

	  if (['left', 'right'].indexOf(first) >= 0) {
	    var _ref = [second, first];
	    first = _ref[0];
	    second = _ref[1];
	  }
	  return [first, second].join(' ');
	}

	function removeFromArray(arr, item) {
	  var index = undefined;
	  var results = [];
	  while ((index = arr.indexOf(item)) !== -1) {
	    results.push(arr.splice(index, 1));
	  }
	  return results;
	}

	var clickEvents = ['click'];
	if ('ontouchstart' in document.documentElement) {
	  clickEvents.push('touchstart');
	}

	var transitionEndEvents = {
	  'WebkitTransition': 'webkitTransitionEnd',
	  'MozTransition': 'transitionend',
	  'OTransition': 'otransitionend',
	  'transition': 'transitionend'
	};

	var transitionEndEvent = '';
	for (var _name in transitionEndEvents) {
	  if (({}).hasOwnProperty.call(transitionEndEvents, _name)) {
	    var tempEl = document.createElement('p');
	    if (typeof tempEl.style[_name] !== 'undefined') {
	      transitionEndEvent = transitionEndEvents[_name];
	    }
	  }
	}

	var MIRROR_ATTACH = {
	  left: 'right',
	  right: 'left',
	  top: 'bottom',
	  bottom: 'top',
	  middle: 'middle',
	  center: 'center'
	};

	var allDrops = {};

	// Drop can be included in external libraries.  Calling createContext gives you a fresh
	// copy of drop which won't interact with other copies on the page (beyond calling the document events).

	function createContext() {
	  var options = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

	  var drop = function drop() {
	    for (var _len = arguments.length, args = Array(_len), _key = 0; _key < _len; _key++) {
	      args[_key] = arguments[_key];
	    }

	    return new (_bind.apply(DropInstance, [null].concat(args)))();
	  };

	  extend(drop, {
	    createContext: createContext,
	    drops: [],
	    defaults: {}
	  });

	  var defaultOptions = {
	    classPrefix: 'drop',
	    defaults: {
	      position: 'bottom left',
	      openOn: 'click',
	      beforeClose: null,
	      constrainToScrollParent: true,
	      constrainToWindow: true,
	      classes: '',
	      remove: false,
	      tetherOptions: {}
	    }
	  };

	  extend(drop, defaultOptions, options);
	  extend(drop.defaults, defaultOptions.defaults, options.defaults);

	  if (typeof allDrops[drop.classPrefix] === 'undefined') {
	    allDrops[drop.classPrefix] = [];
	  }

	  drop.updateBodyClasses = function () {
	    // There is only one body, so despite the context concept, we still iterate through all
	    // drops which share our classPrefix.

	    var anyOpen = false;
	    var drops = allDrops[drop.classPrefix];
	    var len = drops.length;
	    for (var i = 0; i < len; ++i) {
	      if (drops[i].isOpened()) {
	        anyOpen = true;
	        break;
	      }
	    }

	    if (anyOpen) {
	      addClass(document.body, drop.classPrefix + '-open');
	    } else {
	      removeClass(document.body, drop.classPrefix + '-open');
	    }
	  };

	  var DropInstance = (function (_Evented) {
	    _inherits(DropInstance, _Evented);

	    function DropInstance(opts) {
	      _classCallCheck(this, DropInstance);

	      _get(Object.getPrototypeOf(DropInstance.prototype), 'constructor', this).call(this);
	      this.options = extend({}, drop.defaults, opts);
	      this.target = this.options.target;

	      if (typeof this.target === 'undefined') {
	        throw new Error('Drop Error: You must provide a target.');
	      }

	      var dataPrefix = 'data-' + drop.classPrefix;

	      var contentAttr = this.target.getAttribute(dataPrefix);
	      if (contentAttr) {
	        this.options.content = contentAttr;
	      }

	      var attrsOverride = ['position', 'openOn'];
	      for (var i = 0; i < attrsOverride.length; ++i) {

	        var override = this.target.getAttribute(dataPrefix + '-' + attrsOverride[i]);
	        if (override) {
	          this.options[attrsOverride[i]] = override;
	        }
	      }

	      if (this.options.classes && this.options.addTargetClasses !== false) {
	        addClass(this.target, this.options.classes);
	      }

	      drop.drops.push(this);
	      allDrops[drop.classPrefix].push(this);

	      this._boundEvents = [];
	      this.bindMethods();
	      this.setupElements();
	      this.setupEvents();
	      this.setupTether();
	    }

	    _createClass(DropInstance, [{
	      key: '_on',
	      value: function _on(element, event, handler) {
	        this._boundEvents.push({ element: element, event: event, handler: handler });
	        element.addEventListener(event, handler);
	      }
	    }, {
	      key: 'bindMethods',
	      value: function bindMethods() {
	        this.transitionEndHandler = this._transitionEndHandler.bind(this);
	      }
	    }, {
	      key: 'setupElements',
	      value: function setupElements() {
	        var _this = this;

	        this.drop = document.createElement('div');
	        addClass(this.drop, drop.classPrefix);

	        if (this.options.classes) {
	          addClass(this.drop, this.options.classes);
	        }

	        this.content = document.createElement('div');
	        addClass(this.content, drop.classPrefix + '-content');

	        if (typeof this.options.content === 'function') {
	          var generateAndSetContent = function generateAndSetContent() {
	            // content function might return a string or an element
	            var contentElementOrHTML = _this.options.content.call(_this, _this);

	            if (typeof contentElementOrHTML === 'string') {
	              _this.content.innerHTML = contentElementOrHTML;
	            } else if (typeof contentElementOrHTML === 'object') {
	              _this.content.innerHTML = "";
	              _this.content.appendChild(contentElementOrHTML);
	            } else {
	              throw new Error('Drop Error: Content function should return a string or HTMLElement.');
	            }
	          };

	          generateAndSetContent();
	          this.on('open', generateAndSetContent.bind(this));
	        } else if (typeof this.options.content === 'object') {
	          this.content.appendChild(this.options.content);
	        } else {
	          this.content.innerHTML = this.options.content;
	        }

	        this.drop.appendChild(this.content);
	      }
	    }, {
	      key: 'setupTether',
	      value: function setupTether() {
	        // Tether expects two attachment points, one in the target element, one in the
	        // drop.  We use a single one, and use the order as well, to allow us to put
	        // the drop on either side of any of the four corners.  This magic converts between
	        // the two:
	        var dropAttach = this.options.position.split(' ');
	        dropAttach[0] = MIRROR_ATTACH[dropAttach[0]];
	        dropAttach = dropAttach.join(' ');

	        var constraints = [];
	        if (this.options.constrainToScrollParent) {
	          constraints.push({
	            to: 'scrollParent',
	            pin: 'top, bottom',
	            attachment: 'together none'
	          });
	        } else {
	          // To get 'out of bounds' classes
	          constraints.push({
	            to: 'scrollParent'
	          });
	        }

	        if (this.options.constrainToWindow !== false) {
	          constraints.push({
	            to: 'window',
	            attachment: 'together'
	          });
	        } else {
	          // To get 'out of bounds' classes
	          constraints.push({
	            to: 'window'
	          });
	        }

	        var opts = {
	          element: this.drop,
	          target: this.target,
	          attachment: sortAttach(dropAttach),
	          targetAttachment: sortAttach(this.options.position),
	          classPrefix: drop.classPrefix,
	          offset: '0 0',
	          targetOffset: '0 0',
	          enabled: false,
	          constraints: constraints,
	          addTargetClasses: this.options.addTargetClasses
	        };

	        if (this.options.tetherOptions !== false) {
	          this.tether = new Tether(extend({}, opts, this.options.tetherOptions));
	        }
	      }
	    }, {
	      key: 'setupEvents',
	      value: function setupEvents() {
	        var _this2 = this;

	        if (!this.options.openOn) {
	          return;
	        }

	        if (this.options.openOn === 'always') {
	          setTimeout(this.open.bind(this));
	          return;
	        }

	        var events = this.options.openOn.split(' ');

	        if (events.indexOf('click') >= 0) {
	          var openHandler = function openHandler(event) {
	            _this2.toggle(event);
	            event.preventDefault();
	          };

	          var closeHandler = function closeHandler(event) {
	            if (!_this2.isOpened()) {
	              return;
	            }

	            // Clicking inside dropdown
	            if (event.target === _this2.drop || _this2.drop.contains(event.target)) {
	              return;
	            }

	            // Clicking target
	            if (event.target === _this2.target || _this2.target.contains(event.target)) {
	              return;
	            }

	            _this2.close(event);
	          };

	          for (var i = 0; i < clickEvents.length; ++i) {
	            var clickEvent = clickEvents[i];
	            this._on(this.target, clickEvent, openHandler);
	            this._on(document, clickEvent, closeHandler);
	          }
	        }

	        var onUs = false;
	        var outTimeout = null;

	        var focusInHandler = function focusInHandler(event) {
	          onUs = true;
	          _this2.open(event);
	        };

	        var focusOutHandler = function focusOutHandler(event) {
	          onUs = false;

	          if (typeof outTimeout !== 'undefined') {
	            clearTimeout(outTimeout);
	          }

	          outTimeout = setTimeout(function () {
	            if (!onUs) {
	              _this2.close(event);
	            }
	            outTimeout = null;
	          }, 50);
	        };

	        if (events.indexOf('hover') >= 0) {
	          this._on(this.target, 'mouseover', focusInHandler);
	          this._on(this.drop, 'mouseover', focusInHandler);
	          this._on(this.target, 'mouseout', focusOutHandler);
	          this._on(this.drop, 'mouseout', focusOutHandler);
	        }

	        if (events.indexOf('focus') >= 0) {
	          this._on(this.target, 'focus', focusInHandler);
	          this._on(this.drop, 'focus', focusInHandler);
	          this._on(this.target, 'blur', focusOutHandler);
	          this._on(this.drop, 'blur', focusOutHandler);
	        }
	      }
	    }, {
	      key: 'isOpened',
	      value: function isOpened() {
	        if (this.drop) {
	          return hasClass(this.drop, drop.classPrefix + '-open');
	        }
	      }
	    }, {
	      key: 'toggle',
	      value: function toggle(event) {
	        if (this.isOpened()) {
	          this.close(event);
	        } else {
	          this.open(event);
	        }
	      }
	    }, {
	      key: 'open',
	      value: function open(event) {
	        var _this3 = this;

	        if (this.isOpened()) {
	          return;
	        }

	        if (!this.drop.parentNode) {
	          document.body.appendChild(this.drop);
	        }

	        if (typeof this.tether !== 'undefined') {
	          this.tether.enable();
	        }

	        addClass(this.drop, drop.classPrefix + '-open');
	        addClass(this.drop, drop.classPrefix + '-open-transitionend');

	        setTimeout(function () {
	          if (_this3.drop) {
	            addClass(_this3.drop, drop.classPrefix + '-after-open');
	          }
	        });

	        if (typeof this.tether !== 'undefined') {
	          this.tether.position();
	        }

	        this.trigger('open');

	        drop.updateBodyClasses();
	      }
	    }, {
	      key: '_transitionEndHandler',
	      value: function _transitionEndHandler(e) {
	        if (e.target !== e.currentTarget) {
	          return;
	        }

	        if (!hasClass(this.drop, drop.classPrefix + '-open')) {
	          removeClass(this.drop, drop.classPrefix + '-open-transitionend');
	        }
	        this.drop.removeEventListener(transitionEndEvent, this.transitionEndHandler);
	      }
	    }, {
	      key: 'beforeCloseHandler',
	      value: function beforeCloseHandler(event) {
	        var shouldClose = true;

	        if (!this.isClosing && typeof this.options.beforeClose === 'function') {
	          this.isClosing = true;
	          shouldClose = this.options.beforeClose(event, this) !== false;
	        }

	        this.isClosing = false;

	        return shouldClose;
	      }
	    }, {
	      key: 'close',
	      value: function close(event) {
	        if (!this.isOpened()) {
	          return;
	        }

	        if (!this.beforeCloseHandler(event)) {
	          return;
	        }

	        removeClass(this.drop, drop.classPrefix + '-open');
	        removeClass(this.drop, drop.classPrefix + '-after-open');

	        this.drop.addEventListener(transitionEndEvent, this.transitionEndHandler);

	        this.trigger('close');

	        if (typeof this.tether !== 'undefined') {
	          this.tether.disable();
	        }

	        drop.updateBodyClasses();

	        if (this.options.remove) {
	          this.remove(event);
	        }
	      }
	    }, {
	      key: 'remove',
	      value: function remove(event) {
	        this.close(event);
	        if (this.drop.parentNode) {
	          this.drop.parentNode.removeChild(this.drop);
	        }
	      }
	    }, {
	      key: 'position',
	      value: function position() {
	        if (this.isOpened() && typeof this.tether !== 'undefined') {
	          this.tether.position();
	        }
	      }
	    }, {
	      key: 'destroy',
	      value: function destroy() {
	        this.remove();

	        if (typeof this.tether !== 'undefined') {
	          this.tether.destroy();
	        }

	        for (var i = 0; i < this._boundEvents.length; ++i) {
	          var _boundEvents$i = this._boundEvents[i];
	          var element = _boundEvents$i.element;
	          var _event = _boundEvents$i.event;
	          var handler = _boundEvents$i.handler;

	          element.removeEventListener(_event, handler);
	        }

	        this._boundEvents = [];

	        this.tether = null;
	        this.drop = null;
	        this.content = null;
	        this.target = null;

	        removeFromArray(allDrops[drop.classPrefix], this);
	        removeFromArray(drop.drops, this);
	      }
	    }]);

	    return DropInstance;
	  })(Evented);

	  return drop;
	}

	var Drop = createContext();

	document.addEventListener('DOMContentLoaded', function () {
	  Drop.updateBodyClasses();
	});
	return Drop;

	}));


/***/ },
/* 95 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*! tether 1.1.0 */

	(function(root, factory) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof exports === 'object') {
	    module.exports = factory(require, exports, module);
	  } else {
	    root.Tether = factory();
	  }
	}(this, function(require, exports, module) {

	'use strict';

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	var TetherBase = undefined;
	if (typeof TetherBase === 'undefined') {
	  TetherBase = { modules: [] };
	}

	function getScrollParent(el) {
	  var _getComputedStyle = getComputedStyle(el);

	  var position = _getComputedStyle.position;

	  if (position === 'fixed') {
	    return el;
	  }

	  var parent = el;
	  while (parent = parent.parentNode) {
	    var style = undefined;
	    try {
	      style = getComputedStyle(parent);
	    } catch (err) {}

	    if (typeof style === 'undefined' || style === null) {
	      return parent;
	    }

	    var _style = style;
	    var overflow = _style.overflow;
	    var overflowX = _style.overflowX;
	    var overflowY = _style.overflowY;

	    if (/(auto|scroll)/.test(overflow + overflowY + overflowX)) {
	      if (position !== 'absolute' || ['relative', 'absolute', 'fixed'].indexOf(style.position) >= 0) {
	        return parent;
	      }
	    }
	  }

	  return document.body;
	}

	var uniqueId = (function () {
	  var id = 0;
	  return function () {
	    return ++id;
	  };
	})();

	var zeroPosCache = {};
	var getOrigin = function getOrigin(doc) {
	  // getBoundingClientRect is unfortunately too accurate.  It introduces a pixel or two of
	  // jitter as the user scrolls that messes with our ability to detect if two positions
	  // are equivilant or not.  We place an element at the top left of the page that will
	  // get the same jitter, so we can cancel the two out.
	  var node = doc._tetherZeroElement;
	  if (typeof node === 'undefined') {
	    node = doc.createElement('div');
	    node.setAttribute('data-tether-id', uniqueId());
	    extend(node.style, {
	      top: 0,
	      left: 0,
	      position: 'absolute'
	    });

	    doc.body.appendChild(node);

	    doc._tetherZeroElement = node;
	  }

	  var id = node.getAttribute('data-tether-id');
	  if (typeof zeroPosCache[id] === 'undefined') {
	    zeroPosCache[id] = {};

	    var rect = node.getBoundingClientRect();
	    for (var k in rect) {
	      // Can't use extend, as on IE9, elements don't resolve to be hasOwnProperty
	      zeroPosCache[id][k] = rect[k];
	    }

	    // Clear the cache when this position call is done
	    defer(function () {
	      delete zeroPosCache[id];
	    });
	  }

	  return zeroPosCache[id];
	};

	function getBounds(el) {
	  var doc = undefined;
	  if (el === document) {
	    doc = document;
	    el = document.documentElement;
	  } else {
	    doc = el.ownerDocument;
	  }

	  var docEl = doc.documentElement;

	  var box = {};
	  // The original object returned by getBoundingClientRect is immutable, so we clone it
	  // We can't use extend because the properties are not considered part of the object by hasOwnProperty in IE9
	  var rect = el.getBoundingClientRect();
	  for (var k in rect) {
	    box[k] = rect[k];
	  }

	  var origin = getOrigin(doc);

	  box.top -= origin.top;
	  box.left -= origin.left;

	  if (typeof box.width === 'undefined') {
	    box.width = document.body.scrollWidth - box.left - box.right;
	  }
	  if (typeof box.height === 'undefined') {
	    box.height = document.body.scrollHeight - box.top - box.bottom;
	  }

	  box.top = box.top - docEl.clientTop;
	  box.left = box.left - docEl.clientLeft;
	  box.right = doc.body.clientWidth - box.width - box.left;
	  box.bottom = doc.body.clientHeight - box.height - box.top;

	  return box;
	}

	function getOffsetParent(el) {
	  return el.offsetParent || document.documentElement;
	}

	function getScrollBarSize() {
	  var inner = document.createElement('div');
	  inner.style.width = '100%';
	  inner.style.height = '200px';

	  var outer = document.createElement('div');
	  extend(outer.style, {
	    position: 'absolute',
	    top: 0,
	    left: 0,
	    pointerEvents: 'none',
	    visibility: 'hidden',
	    width: '200px',
	    height: '150px',
	    overflow: 'hidden'
	  });

	  outer.appendChild(inner);

	  document.body.appendChild(outer);

	  var widthContained = inner.offsetWidth;
	  outer.style.overflow = 'scroll';
	  var widthScroll = inner.offsetWidth;

	  if (widthContained === widthScroll) {
	    widthScroll = outer.clientWidth;
	  }

	  document.body.removeChild(outer);

	  var width = widthContained - widthScroll;

	  return { width: width, height: width };
	}

	function extend() {
	  var out = arguments.length <= 0 || arguments[0] === undefined ? {} : arguments[0];

	  var args = [];

	  Array.prototype.push.apply(args, arguments);

	  args.slice(1).forEach(function (obj) {
	    if (obj) {
	      for (var key in obj) {
	        if (({}).hasOwnProperty.call(obj, key)) {
	          out[key] = obj[key];
	        }
	      }
	    }
	  });

	  return out;
	}

	function removeClass(el, name) {
	  if (typeof el.classList !== 'undefined') {
	    name.split(' ').forEach(function (cls) {
	      if (cls.trim()) {
	        el.classList.remove(cls);
	      }
	    });
	  } else {
	    var regex = new RegExp('(^| )' + name.split(' ').join('|') + '( |$)', 'gi');
	    var className = getClassName(el).replace(regex, ' ');
	    setClassName(el, className);
	  }
	}

	function addClass(el, name) {
	  if (typeof el.classList !== 'undefined') {
	    name.split(' ').forEach(function (cls) {
	      if (cls.trim()) {
	        el.classList.add(cls);
	      }
	    });
	  } else {
	    removeClass(el, name);
	    var cls = getClassName(el) + (' ' + name);
	    setClassName(el, cls);
	  }
	}

	function hasClass(el, name) {
	  if (typeof el.classList !== 'undefined') {
	    return el.classList.contains(name);
	  }
	  var className = getClassName(el);
	  return new RegExp('(^| )' + name + '( |$)', 'gi').test(className);
	}

	function getClassName(el) {
	  if (el.className instanceof SVGAnimatedString) {
	    return el.className.baseVal;
	  }
	  return el.className;
	}

	function setClassName(el, className) {
	  el.setAttribute('class', className);
	}

	function updateClasses(el, add, all) {
	  // Of the set of 'all' classes, we need the 'add' classes, and only the
	  // 'add' classes to be set.
	  all.forEach(function (cls) {
	    if (add.indexOf(cls) === -1 && hasClass(el, cls)) {
	      removeClass(el, cls);
	    }
	  });

	  add.forEach(function (cls) {
	    if (!hasClass(el, cls)) {
	      addClass(el, cls);
	    }
	  });
	}

	var deferred = [];

	var defer = function defer(fn) {
	  deferred.push(fn);
	};

	var flush = function flush() {
	  var fn = undefined;
	  while (fn = deferred.pop()) {
	    fn();
	  }
	};

	var Evented = (function () {
	  function Evented() {
	    _classCallCheck(this, Evented);
	  }

	  _createClass(Evented, [{
	    key: 'on',
	    value: function on(event, handler, ctx) {
	      var once = arguments.length <= 3 || arguments[3] === undefined ? false : arguments[3];

	      if (typeof this.bindings === 'undefined') {
	        this.bindings = {};
	      }
	      if (typeof this.bindings[event] === 'undefined') {
	        this.bindings[event] = [];
	      }
	      this.bindings[event].push({ handler: handler, ctx: ctx, once: once });
	    }
	  }, {
	    key: 'once',
	    value: function once(event, handler, ctx) {
	      this.on(event, handler, ctx, true);
	    }
	  }, {
	    key: 'off',
	    value: function off(event, handler) {
	      if (typeof this.bindings !== 'undefined' && typeof this.bindings[event] !== 'undefined') {
	        return;
	      }

	      if (typeof handler === 'undefined') {
	        delete this.bindings[event];
	      } else {
	        var i = 0;
	        while (i < this.bindings[event].length) {
	          if (this.bindings[event][i].handler === handler) {
	            this.bindings[event].splice(i, 1);
	          } else {
	            ++i;
	          }
	        }
	      }
	    }
	  }, {
	    key: 'trigger',
	    value: function trigger(event) {
	      if (typeof this.bindings !== 'undefined' && this.bindings[event]) {
	        var i = 0;

	        for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	          args[_key - 1] = arguments[_key];
	        }

	        while (i < this.bindings[event].length) {
	          var _bindings$event$i = this.bindings[event][i];
	          var handler = _bindings$event$i.handler;
	          var ctx = _bindings$event$i.ctx;
	          var once = _bindings$event$i.once;

	          var context = ctx;
	          if (typeof context === 'undefined') {
	            context = this;
	          }

	          handler.apply(context, args);

	          if (once) {
	            this.bindings[event].splice(i, 1);
	          } else {
	            ++i;
	          }
	        }
	      }
	    }
	  }]);

	  return Evented;
	})();

	TetherBase.Utils = {
	  getScrollParent: getScrollParent,
	  getBounds: getBounds,
	  getOffsetParent: getOffsetParent,
	  extend: extend,
	  addClass: addClass,
	  removeClass: removeClass,
	  hasClass: hasClass,
	  updateClasses: updateClasses,
	  defer: defer,
	  flush: flush,
	  uniqueId: uniqueId,
	  Evented: Evented,
	  getScrollBarSize: getScrollBarSize
	};
	/* globals TetherBase, performance */

	'use strict';

	var _slicedToArray = (function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i['return']) _i['return'](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError('Invalid attempt to destructure non-iterable instance'); } }; })();

	var _createClass = (function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ('value' in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; })();

	function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError('Cannot call a class as a function'); } }

	if (typeof TetherBase === 'undefined') {
	  throw new Error('You must include the utils.js file before tether.js');
	}

	var _TetherBase$Utils = TetherBase.Utils;
	var getScrollParent = _TetherBase$Utils.getScrollParent;
	var getBounds = _TetherBase$Utils.getBounds;
	var getOffsetParent = _TetherBase$Utils.getOffsetParent;
	var extend = _TetherBase$Utils.extend;
	var addClass = _TetherBase$Utils.addClass;
	var removeClass = _TetherBase$Utils.removeClass;
	var updateClasses = _TetherBase$Utils.updateClasses;
	var defer = _TetherBase$Utils.defer;
	var flush = _TetherBase$Utils.flush;
	var getScrollBarSize = _TetherBase$Utils.getScrollBarSize;

	function within(a, b) {
	  var diff = arguments.length <= 2 || arguments[2] === undefined ? 1 : arguments[2];

	  return a + diff >= b && b >= a - diff;
	}

	var transformKey = (function () {
	  if (typeof document === 'undefined') {
	    return '';
	  }
	  var el = document.createElement('div');

	  var transforms = ['transform', 'webkitTransform', 'OTransform', 'MozTransform', 'msTransform'];
	  for (var i = 0; i < transforms.length; ++i) {
	    var key = transforms[i];
	    if (el.style[key] !== undefined) {
	      return key;
	    }
	  }
	})();

	var tethers = [];

	var position = function position() {
	  tethers.forEach(function (tether) {
	    tether.position(false);
	  });
	  flush();
	};

	function now() {
	  if (typeof performance !== 'undefined' && typeof performance.now !== 'undefined') {
	    return performance.now();
	  }
	  return +new Date();
	}

	(function () {
	  var lastCall = null;
	  var lastDuration = null;
	  var pendingTimeout = null;

	  var tick = function tick() {
	    if (typeof lastDuration !== 'undefined' && lastDuration > 16) {
	      // We voluntarily throttle ourselves if we can't manage 60fps
	      lastDuration = Math.min(lastDuration - 16, 250);

	      // Just in case this is the last event, remember to position just once more
	      pendingTimeout = setTimeout(tick, 250);
	      return;
	    }

	    if (typeof lastCall !== 'undefined' && now() - lastCall < 10) {
	      // Some browsers call events a little too frequently, refuse to run more than is reasonable
	      return;
	    }

	    if (typeof pendingTimeout !== 'undefined') {
	      clearTimeout(pendingTimeout);
	      pendingTimeout = null;
	    }

	    lastCall = now();
	    position();
	    lastDuration = now() - lastCall;
	  };

	  if (typeof window !== 'undefined') {
	    ['resize', 'scroll', 'touchmove'].forEach(function (event) {
	      window.addEventListener(event, tick);
	    });
	  }
	})();

	var MIRROR_LR = {
	  center: 'center',
	  left: 'right',
	  right: 'left'
	};

	var MIRROR_TB = {
	  middle: 'middle',
	  top: 'bottom',
	  bottom: 'top'
	};

	var OFFSET_MAP = {
	  top: 0,
	  left: 0,
	  middle: '50%',
	  center: '50%',
	  bottom: '100%',
	  right: '100%'
	};

	var autoToFixedAttachment = function autoToFixedAttachment(attachment, relativeToAttachment) {
	  var left = attachment.left;
	  var top = attachment.top;

	  if (left === 'auto') {
	    left = MIRROR_LR[relativeToAttachment.left];
	  }

	  if (top === 'auto') {
	    top = MIRROR_TB[relativeToAttachment.top];
	  }

	  return { left: left, top: top };
	};

	var attachmentToOffset = function attachmentToOffset(attachment) {
	  var left = attachment.left;
	  var top = attachment.top;

	  if (typeof OFFSET_MAP[attachment.left] !== 'undefined') {
	    left = OFFSET_MAP[attachment.left];
	  }

	  if (typeof OFFSET_MAP[attachment.top] !== 'undefined') {
	    top = OFFSET_MAP[attachment.top];
	  }

	  return { left: left, top: top };
	};

	function addOffset() {
	  var out = { top: 0, left: 0 };

	  for (var _len = arguments.length, offsets = Array(_len), _key = 0; _key < _len; _key++) {
	    offsets[_key] = arguments[_key];
	  }

	  offsets.forEach(function (_ref) {
	    var top = _ref.top;
	    var left = _ref.left;

	    if (typeof top === 'string') {
	      top = parseFloat(top, 10);
	    }
	    if (typeof left === 'string') {
	      left = parseFloat(left, 10);
	    }

	    out.top += top;
	    out.left += left;
	  });

	  return out;
	}

	function offsetToPx(offset, size) {
	  if (typeof offset.left === 'string' && offset.left.indexOf('%') !== -1) {
	    offset.left = parseFloat(offset.left, 10) / 100 * size.width;
	  }
	  if (typeof offset.top === 'string' && offset.top.indexOf('%') !== -1) {
	    offset.top = parseFloat(offset.top, 10) / 100 * size.height;
	  }

	  return offset;
	}

	var parseOffset = function parseOffset(value) {
	  var _value$split = value.split(' ');

	  var _value$split2 = _slicedToArray(_value$split, 2);

	  var top = _value$split2[0];
	  var left = _value$split2[1];

	  return { top: top, left: left };
	};
	var parseAttachment = parseOffset;

	var TetherClass = (function () {
	  function TetherClass(options) {
	    var _this = this;

	    _classCallCheck(this, TetherClass);

	    this.position = this.position.bind(this);

	    tethers.push(this);

	    this.history = [];

	    this.setOptions(options, false);

	    TetherBase.modules.forEach(function (module) {
	      if (typeof module.initialize !== 'undefined') {
	        module.initialize.call(_this);
	      }
	    });

	    this.position();
	  }

	  _createClass(TetherClass, [{
	    key: 'getClass',
	    value: function getClass() {
	      var key = arguments.length <= 0 || arguments[0] === undefined ? '' : arguments[0];
	      var classes = this.options.classes;

	      if (typeof classes !== 'undefined' && classes[key]) {
	        return this.options.classes[key];
	      } else if (this.options.classPrefix) {
	        return this.options.classPrefix + '-' + key;
	      } else {
	        return key;
	      }
	    }
	  }, {
	    key: 'setOptions',
	    value: function setOptions(options) {
	      var _this2 = this;

	      var pos = arguments.length <= 1 || arguments[1] === undefined ? true : arguments[1];

	      var defaults = {
	        offset: '0 0',
	        targetOffset: '0 0',
	        targetAttachment: 'auto auto',
	        classPrefix: 'tether'
	      };

	      this.options = extend(defaults, options);

	      var _options = this.options;
	      var element = _options.element;
	      var target = _options.target;
	      var targetModifier = _options.targetModifier;

	      this.element = element;
	      this.target = target;
	      this.targetModifier = targetModifier;

	      if (this.target === 'viewport') {
	        this.target = document.body;
	        this.targetModifier = 'visible';
	      } else if (this.target === 'scroll-handle') {
	        this.target = document.body;
	        this.targetModifier = 'scroll-handle';
	      }

	      ['element', 'target'].forEach(function (key) {
	        if (typeof _this2[key] === 'undefined') {
	          throw new Error('Tether Error: Both element and target must be defined');
	        }

	        if (typeof _this2[key].jquery !== 'undefined') {
	          _this2[key] = _this2[key][0];
	        } else if (typeof _this2[key] === 'string') {
	          _this2[key] = document.querySelector(_this2[key]);
	        }
	      });

	      addClass(this.element, this.getClass('element'));
	      if (!(this.options.addTargetClasses === false)) {
	        addClass(this.target, this.getClass('target'));
	      }

	      if (!this.options.attachment) {
	        throw new Error('Tether Error: You must provide an attachment');
	      }

	      this.targetAttachment = parseAttachment(this.options.targetAttachment);
	      this.attachment = parseAttachment(this.options.attachment);
	      this.offset = parseOffset(this.options.offset);
	      this.targetOffset = parseOffset(this.options.targetOffset);

	      if (typeof this.scrollParent !== 'undefined') {
	        this.disable();
	      }

	      if (this.targetModifier === 'scroll-handle') {
	        this.scrollParent = this.target;
	      } else {
	        this.scrollParent = getScrollParent(this.target);
	      }

	      if (!(this.options.enabled === false)) {
	        this.enable(pos);
	      }
	    }
	  }, {
	    key: 'getTargetBounds',
	    value: function getTargetBounds() {
	      if (typeof this.targetModifier !== 'undefined') {
	        if (this.targetModifier === 'visible') {
	          if (this.target === document.body) {
	            return { top: pageYOffset, left: pageXOffset, height: innerHeight, width: innerWidth };
	          } else {
	            var bounds = getBounds(this.target);

	            var out = {
	              height: bounds.height,
	              width: bounds.width,
	              top: bounds.top,
	              left: bounds.left
	            };

	            out.height = Math.min(out.height, bounds.height - (pageYOffset - bounds.top));
	            out.height = Math.min(out.height, bounds.height - (bounds.top + bounds.height - (pageYOffset + innerHeight)));
	            out.height = Math.min(innerHeight, out.height);
	            out.height -= 2;

	            out.width = Math.min(out.width, bounds.width - (pageXOffset - bounds.left));
	            out.width = Math.min(out.width, bounds.width - (bounds.left + bounds.width - (pageXOffset + innerWidth)));
	            out.width = Math.min(innerWidth, out.width);
	            out.width -= 2;

	            if (out.top < pageYOffset) {
	              out.top = pageYOffset;
	            }
	            if (out.left < pageXOffset) {
	              out.left = pageXOffset;
	            }

	            return out;
	          }
	        } else if (this.targetModifier === 'scroll-handle') {
	          var bounds = undefined;
	          var target = this.target;
	          if (target === document.body) {
	            target = document.documentElement;

	            bounds = {
	              left: pageXOffset,
	              top: pageYOffset,
	              height: innerHeight,
	              width: innerWidth
	            };
	          } else {
	            bounds = getBounds(target);
	          }

	          var style = getComputedStyle(target);

	          var hasBottomScroll = target.scrollWidth > target.clientWidth || [style.overflow, style.overflowX].indexOf('scroll') >= 0 || this.target !== document.body;

	          var scrollBottom = 0;
	          if (hasBottomScroll) {
	            scrollBottom = 15;
	          }

	          var height = bounds.height - parseFloat(style.borderTopWidth) - parseFloat(style.borderBottomWidth) - scrollBottom;

	          var out = {
	            width: 15,
	            height: height * 0.975 * (height / target.scrollHeight),
	            left: bounds.left + bounds.width - parseFloat(style.borderLeftWidth) - 15
	          };

	          var fitAdj = 0;
	          if (height < 408 && this.target === document.body) {
	            fitAdj = -0.00011 * Math.pow(height, 2) - 0.00727 * height + 22.58;
	          }

	          if (this.target !== document.body) {
	            out.height = Math.max(out.height, 24);
	          }

	          var scrollPercentage = this.target.scrollTop / (target.scrollHeight - height);
	          out.top = scrollPercentage * (height - out.height - fitAdj) + bounds.top + parseFloat(style.borderTopWidth);

	          if (this.target === document.body) {
	            out.height = Math.max(out.height, 24);
	          }

	          return out;
	        }
	      } else {
	        return getBounds(this.target);
	      }
	    }
	  }, {
	    key: 'clearCache',
	    value: function clearCache() {
	      this._cache = {};
	    }
	  }, {
	    key: 'cache',
	    value: function cache(k, getter) {
	      // More than one module will often need the same DOM info, so
	      // we keep a cache which is cleared on each position call
	      if (typeof this._cache === 'undefined') {
	        this._cache = {};
	      }

	      if (typeof this._cache[k] === 'undefined') {
	        this._cache[k] = getter.call(this);
	      }

	      return this._cache[k];
	    }
	  }, {
	    key: 'enable',
	    value: function enable() {
	      var pos = arguments.length <= 0 || arguments[0] === undefined ? true : arguments[0];

	      if (!(this.options.addTargetClasses === false)) {
	        addClass(this.target, this.getClass('enabled'));
	      }
	      addClass(this.element, this.getClass('enabled'));
	      this.enabled = true;

	      if (this.scrollParent !== document) {
	        this.scrollParent.addEventListener('scroll', this.position);
	      }

	      if (pos) {
	        this.position();
	      }
	    }
	  }, {
	    key: 'disable',
	    value: function disable() {
	      removeClass(this.target, this.getClass('enabled'));
	      removeClass(this.element, this.getClass('enabled'));
	      this.enabled = false;

	      if (typeof this.scrollParent !== 'undefined') {
	        this.scrollParent.removeEventListener('scroll', this.position);
	      }
	    }
	  }, {
	    key: 'destroy',
	    value: function destroy() {
	      var _this3 = this;

	      this.disable();

	      tethers.forEach(function (tether, i) {
	        if (tether === _this3) {
	          tethers.splice(i, 1);
	          return;
	        }
	      });
	    }
	  }, {
	    key: 'updateAttachClasses',
	    value: function updateAttachClasses(elementAttach, targetAttach) {
	      var _this4 = this;

	      elementAttach = elementAttach || this.attachment;
	      targetAttach = targetAttach || this.targetAttachment;
	      var sides = ['left', 'top', 'bottom', 'right', 'middle', 'center'];

	      if (typeof this._addAttachClasses !== 'undefined' && this._addAttachClasses.length) {
	        // updateAttachClasses can be called more than once in a position call, so
	        // we need to clean up after ourselves such that when the last defer gets
	        // ran it doesn't add any extra classes from previous calls.
	        this._addAttachClasses.splice(0, this._addAttachClasses.length);
	      }

	      if (typeof this._addAttachClasses === 'undefined') {
	        this._addAttachClasses = [];
	      }
	      var add = this._addAttachClasses;

	      if (elementAttach.top) {
	        add.push(this.getClass('element-attached') + '-' + elementAttach.top);
	      }
	      if (elementAttach.left) {
	        add.push(this.getClass('element-attached') + '-' + elementAttach.left);
	      }
	      if (targetAttach.top) {
	        add.push(this.getClass('target-attached') + '-' + targetAttach.top);
	      }
	      if (targetAttach.left) {
	        add.push(this.getClass('target-attached') + '-' + targetAttach.left);
	      }

	      var all = [];
	      sides.forEach(function (side) {
	        all.push(_this4.getClass('element-attached') + '-' + side);
	        all.push(_this4.getClass('target-attached') + '-' + side);
	      });

	      defer(function () {
	        if (!(typeof _this4._addAttachClasses !== 'undefined')) {
	          return;
	        }

	        updateClasses(_this4.element, _this4._addAttachClasses, all);
	        if (!(_this4.options.addTargetClasses === false)) {
	          updateClasses(_this4.target, _this4._addAttachClasses, all);
	        }

	        delete _this4._addAttachClasses;
	      });
	    }
	  }, {
	    key: 'position',
	    value: function position() {
	      var _this5 = this;

	      var flushChanges = arguments.length <= 0 || arguments[0] === undefined ? true : arguments[0];

	      // flushChanges commits the changes immediately, leave true unless you are positioning multiple
	      // tethers (in which case call Tether.Utils.flush yourself when you're done)

	      if (!this.enabled) {
	        return;
	      }

	      this.clearCache();

	      // Turn 'auto' attachments into the appropriate corner or edge
	      var targetAttachment = autoToFixedAttachment(this.targetAttachment, this.attachment);

	      this.updateAttachClasses(this.attachment, targetAttachment);

	      var elementPos = this.cache('element-bounds', function () {
	        return getBounds(_this5.element);
	      });

	      var width = elementPos.width;
	      var height = elementPos.height;

	      if (width === 0 && height === 0 && typeof this.lastSize !== 'undefined') {
	        var _lastSize = this.lastSize;

	        // We cache the height and width to make it possible to position elements that are
	        // getting hidden.
	        width = _lastSize.width;
	        height = _lastSize.height;
	      } else {
	        this.lastSize = { width: width, height: height };
	      }

	      var targetPos = this.cache('target-bounds', function () {
	        return _this5.getTargetBounds();
	      });
	      var targetSize = targetPos;

	      // Get an actual px offset from the attachment
	      var offset = offsetToPx(attachmentToOffset(this.attachment), { width: width, height: height });
	      var targetOffset = offsetToPx(attachmentToOffset(targetAttachment), targetSize);

	      var manualOffset = offsetToPx(this.offset, { width: width, height: height });
	      var manualTargetOffset = offsetToPx(this.targetOffset, targetSize);

	      // Add the manually provided offset
	      offset = addOffset(offset, manualOffset);
	      targetOffset = addOffset(targetOffset, manualTargetOffset);

	      // It's now our goal to make (element position + offset) == (target position + target offset)
	      var left = targetPos.left + targetOffset.left - offset.left;
	      var top = targetPos.top + targetOffset.top - offset.top;

	      for (var i = 0; i < TetherBase.modules.length; ++i) {
	        var _module2 = TetherBase.modules[i];
	        var ret = _module2.position.call(this, {
	          left: left,
	          top: top,
	          targetAttachment: targetAttachment,
	          targetPos: targetPos,
	          elementPos: elementPos,
	          offset: offset,
	          targetOffset: targetOffset,
	          manualOffset: manualOffset,
	          manualTargetOffset: manualTargetOffset,
	          scrollbarSize: scrollbarSize,
	          attachment: this.attachment
	        });

	        if (ret === false) {
	          return false;
	        } else if (typeof ret === 'undefined' || typeof ret !== 'object') {
	          continue;
	        } else {
	          top = ret.top;
	          left = ret.left;
	        }
	      }

	      // We describe the position three different ways to give the optimizer
	      // a chance to decide the best possible way to position the element
	      // with the fewest repaints.
	      var next = {
	        // It's position relative to the page (absolute positioning when
	        // the element is a child of the body)
	        page: {
	          top: top,
	          left: left
	        },

	        // It's position relative to the viewport (fixed positioning)
	        viewport: {
	          top: top - pageYOffset,
	          bottom: pageYOffset - top - height + innerHeight,
	          left: left - pageXOffset,
	          right: pageXOffset - left - width + innerWidth
	        }
	      };

	      var scrollbarSize = undefined;
	      if (document.body.scrollWidth > window.innerWidth) {
	        scrollbarSize = this.cache('scrollbar-size', getScrollBarSize);
	        next.viewport.bottom -= scrollbarSize.height;
	      }

	      if (document.body.scrollHeight > window.innerHeight) {
	        scrollbarSize = this.cache('scrollbar-size', getScrollBarSize);
	        next.viewport.right -= scrollbarSize.width;
	      }

	      if (['', 'static'].indexOf(document.body.style.position) === -1 || ['', 'static'].indexOf(document.body.parentElement.style.position) === -1) {
	        // Absolute positioning in the body will be relative to the page, not the 'initial containing block'
	        next.page.bottom = document.body.scrollHeight - top - height;
	        next.page.right = document.body.scrollWidth - left - width;
	      }

	      if (typeof this.options.optimizations !== 'undefined' && this.options.optimizations.moveElement !== false && !(typeof this.targetModifier !== 'undefined')) {
	        (function () {
	          var offsetParent = _this5.cache('target-offsetparent', function () {
	            return getOffsetParent(_this5.target);
	          });
	          var offsetPosition = _this5.cache('target-offsetparent-bounds', function () {
	            return getBounds(offsetParent);
	          });
	          var offsetParentStyle = getComputedStyle(offsetParent);
	          var offsetParentSize = offsetPosition;

	          var offsetBorder = {};
	          ['Top', 'Left', 'Bottom', 'Right'].forEach(function (side) {
	            offsetBorder[side.toLowerCase()] = parseFloat(offsetParentStyle['border' + side + 'Width']);
	          });

	          offsetPosition.right = document.body.scrollWidth - offsetPosition.left - offsetParentSize.width + offsetBorder.right;
	          offsetPosition.bottom = document.body.scrollHeight - offsetPosition.top - offsetParentSize.height + offsetBorder.bottom;

	          if (next.page.top >= offsetPosition.top + offsetBorder.top && next.page.bottom >= offsetPosition.bottom) {
	            if (next.page.left >= offsetPosition.left + offsetBorder.left && next.page.right >= offsetPosition.right) {
	              // We're within the visible part of the target's scroll parent
	              var scrollTop = offsetParent.scrollTop;
	              var scrollLeft = offsetParent.scrollLeft;

	              // It's position relative to the target's offset parent (absolute positioning when
	              // the element is moved to be a child of the target's offset parent).
	              next.offset = {
	                top: next.page.top - offsetPosition.top + scrollTop - offsetBorder.top,
	                left: next.page.left - offsetPosition.left + scrollLeft - offsetBorder.left
	              };
	            }
	          }
	        })();
	      }

	      // We could also travel up the DOM and try each containing context, rather than only
	      // looking at the body, but we're gonna get diminishing returns.

	      this.move(next);

	      this.history.unshift(next);

	      if (this.history.length > 3) {
	        this.history.pop();
	      }

	      if (flushChanges) {
	        flush();
	      }

	      return true;
	    }

	    // THE ISSUE
	  }, {
	    key: 'move',
	    value: function move(pos) {
	      var _this6 = this;

	      if (!(typeof this.element.parentNode !== 'undefined')) {
	        return;
	      }

	      var same = {};

	      for (var type in pos) {
	        same[type] = {};

	        for (var key in pos[type]) {
	          var found = false;

	          for (var i = 0; i < this.history.length; ++i) {
	            var point = this.history[i];
	            if (typeof point[type] !== 'undefined' && !within(point[type][key], pos[type][key])) {
	              found = true;
	              break;
	            }
	          }

	          if (!found) {
	            same[type][key] = true;
	          }
	        }
	      }

	      var css = { top: '', left: '', right: '', bottom: '' };

	      var transcribe = function transcribe(_same, _pos) {
	        var hasOptimizations = typeof _this6.options.optimizations !== 'undefined';
	        var gpu = hasOptimizations ? _this6.options.optimizations.gpu : null;
	        if (gpu !== false) {
	          var yPos = undefined,
	              xPos = undefined;
	          if (_same.top) {
	            css.top = 0;
	            yPos = _pos.top;
	          } else {
	            css.bottom = 0;
	            yPos = -_pos.bottom;
	          }

	          if (_same.left) {
	            css.left = 0;
	            xPos = _pos.left;
	          } else {
	            css.right = 0;
	            xPos = -_pos.right;
	          }

	          css[transformKey] = 'translateX(' + Math.round(xPos) + 'px) translateY(' + Math.round(yPos) + 'px)';

	          if (transformKey !== 'msTransform') {
	            // The Z transform will keep this in the GPU (faster, and prevents artifacts),
	            // but IE9 doesn't support 3d transforms and will choke.
	            css[transformKey] += " translateZ(0)";
	          }
	        } else {
	          if (_same.top) {
	            css.top = _pos.top + 'px';
	          } else {
	            css.bottom = _pos.bottom + 'px';
	          }

	          if (_same.left) {
	            css.left = _pos.left + 'px';
	          } else {
	            css.right = _pos.right + 'px';
	          }
	        }
	      };

	      var moved = false;
	      if ((same.page.top || same.page.bottom) && (same.page.left || same.page.right)) {
	        css.position = 'absolute';
	        transcribe(same.page, pos.page);
	      } else if ((same.viewport.top || same.viewport.bottom) && (same.viewport.left || same.viewport.right)) {
	        css.position = 'fixed';
	        transcribe(same.viewport, pos.viewport);
	      } else if (typeof same.offset !== 'undefined' && same.offset.top && same.offset.left) {
	        (function () {
	          css.position = 'absolute';
	          var offsetParent = _this6.cache('target-offsetparent', function () {
	            return getOffsetParent(_this6.target);
	          });

	          if (getOffsetParent(_this6.element) !== offsetParent) {
	            defer(function () {
	              _this6.element.parentNode.removeChild(_this6.element);
	              offsetParent.appendChild(_this6.element);
	            });
	          }

	          transcribe(same.offset, pos.offset);
	          moved = true;
	        })();
	      } else {
	        css.position = 'absolute';
	        transcribe({ top: true, left: true }, pos.page);
	      }

	      if (!moved) {
	        var offsetParentIsBody = true;
	        var currentNode = this.element.parentNode;
	        while (currentNode && currentNode.tagName !== 'BODY') {
	          if (getComputedStyle(currentNode).position !== 'static') {
	            offsetParentIsBody = false;
	            break;
	          }

	          currentNode = currentNode.parentNode;
	        }

	        if (!offsetParentIsBody) {
	          this.element.parentNode.removeChild(this.element);
	          document.body.appendChild(this.element);
	        }
	      }

	      // Any css change will trigger a repaint, so let's avoid one if nothing changed
	      var writeCSS = {};
	      var write = false;
	      for (var key in css) {
	        var val = css[key];
	        var elVal = this.element.style[key];

	        if (elVal !== '' && val !== '' && ['top', 'left', 'bottom', 'right'].indexOf(key) >= 0) {
	          elVal = parseFloat(elVal);
	          val = parseFloat(val);
	        }

	        if (elVal !== val) {
	          write = true;
	          writeCSS[key] = val;
	        }
	      }

	      if (write) {
	        defer(function () {
	          extend(_this6.element.style, writeCSS);
	        });
	      }
	    }
	  }]);

	  return TetherClass;
	})();

	TetherClass.modules = [];

	TetherBase.position = position;

	var Tether = extend(TetherClass, TetherBase);
	/* globals TetherBase */

	'use strict';

	var _slicedToArray = (function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i['return']) _i['return'](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError('Invalid attempt to destructure non-iterable instance'); } }; })();

	var _TetherBase$Utils = TetherBase.Utils;
	var getBounds = _TetherBase$Utils.getBounds;
	var extend = _TetherBase$Utils.extend;
	var updateClasses = _TetherBase$Utils.updateClasses;
	var defer = _TetherBase$Utils.defer;

	var BOUNDS_FORMAT = ['left', 'top', 'right', 'bottom'];

	function getBoundingRect(tether, to) {
	  if (to === 'scrollParent') {
	    to = tether.scrollParent;
	  } else if (to === 'window') {
	    to = [pageXOffset, pageYOffset, innerWidth + pageXOffset, innerHeight + pageYOffset];
	  }

	  if (to === document) {
	    to = to.documentElement;
	  }

	  if (typeof to.nodeType !== 'undefined') {
	    (function () {
	      var size = getBounds(to);
	      var pos = size;
	      var style = getComputedStyle(to);

	      to = [pos.left, pos.top, size.width + pos.left, size.height + pos.top];

	      BOUNDS_FORMAT.forEach(function (side, i) {
	        side = side[0].toUpperCase() + side.substr(1);
	        if (side === 'Top' || side === 'Left') {
	          to[i] += parseFloat(style['border' + side + 'Width']);
	        } else {
	          to[i] -= parseFloat(style['border' + side + 'Width']);
	        }
	      });
	    })();
	  }

	  return to;
	}

	TetherBase.modules.push({
	  position: function position(_ref) {
	    var _this = this;

	    var top = _ref.top;
	    var left = _ref.left;
	    var targetAttachment = _ref.targetAttachment;

	    if (!this.options.constraints) {
	      return true;
	    }

	    var _cache = this.cache('element-bounds', function () {
	      return getBounds(_this.element);
	    });

	    var height = _cache.height;
	    var width = _cache.width;

	    if (width === 0 && height === 0 && typeof this.lastSize !== 'undefined') {
	      var _lastSize = this.lastSize;

	      // Handle the item getting hidden as a result of our positioning without glitching
	      // the classes in and out
	      width = _lastSize.width;
	      height = _lastSize.height;
	    }

	    var targetSize = this.cache('target-bounds', function () {
	      return _this.getTargetBounds();
	    });

	    var targetHeight = targetSize.height;
	    var targetWidth = targetSize.width;

	    var allClasses = [this.getClass('pinned'), this.getClass('out-of-bounds')];

	    this.options.constraints.forEach(function (constraint) {
	      var outOfBoundsClass = constraint.outOfBoundsClass;
	      var pinnedClass = constraint.pinnedClass;

	      if (outOfBoundsClass) {
	        allClasses.push(outOfBoundsClass);
	      }
	      if (pinnedClass) {
	        allClasses.push(pinnedClass);
	      }
	    });

	    allClasses.forEach(function (cls) {
	      ['left', 'top', 'right', 'bottom'].forEach(function (side) {
	        allClasses.push(cls + '-' + side);
	      });
	    });

	    var addClasses = [];

	    var tAttachment = extend({}, targetAttachment);
	    var eAttachment = extend({}, this.attachment);

	    this.options.constraints.forEach(function (constraint) {
	      var to = constraint.to;
	      var attachment = constraint.attachment;
	      var pin = constraint.pin;

	      if (typeof attachment === 'undefined') {
	        attachment = '';
	      }

	      var changeAttachX = undefined,
	          changeAttachY = undefined;
	      if (attachment.indexOf(' ') >= 0) {
	        var _attachment$split = attachment.split(' ');

	        var _attachment$split2 = _slicedToArray(_attachment$split, 2);

	        changeAttachY = _attachment$split2[0];
	        changeAttachX = _attachment$split2[1];
	      } else {
	        changeAttachX = changeAttachY = attachment;
	      }

	      var bounds = getBoundingRect(_this, to);

	      if (changeAttachY === 'target' || changeAttachY === 'both') {
	        if (top < bounds[1] && tAttachment.top === 'top') {
	          top += targetHeight;
	          tAttachment.top = 'bottom';
	        }

	        if (top + height > bounds[3] && tAttachment.top === 'bottom') {
	          top -= targetHeight;
	          tAttachment.top = 'top';
	        }
	      }

	      if (changeAttachY === 'together') {
	        if (top < bounds[1] && tAttachment.top === 'top') {
	          if (eAttachment.top === 'bottom') {
	            top += targetHeight;
	            tAttachment.top = 'bottom';

	            top += height;
	            eAttachment.top = 'top';
	          } else if (eAttachment.top === 'top') {
	            top += targetHeight;
	            tAttachment.top = 'bottom';

	            top -= height;
	            eAttachment.top = 'bottom';
	          }
	        }

	        if (top + height > bounds[3] && tAttachment.top === 'bottom') {
	          if (eAttachment.top === 'top') {
	            top -= targetHeight;
	            tAttachment.top = 'top';

	            top -= height;
	            eAttachment.top = 'bottom';
	          } else if (eAttachment.top === 'bottom') {
	            top -= targetHeight;
	            tAttachment.top = 'top';

	            top += height;
	            eAttachment.top = 'top';
	          }
	        }

	        if (tAttachment.top === 'middle') {
	          if (top + height > bounds[3] && eAttachment.top === 'top') {
	            top -= height;
	            eAttachment.top = 'bottom';
	          } else if (top < bounds[1] && eAttachment.top === 'bottom') {
	            top += height;
	            eAttachment.top = 'top';
	          }
	        }
	      }

	      if (changeAttachX === 'target' || changeAttachX === 'both') {
	        if (left < bounds[0] && tAttachment.left === 'left') {
	          left += targetWidth;
	          tAttachment.left = 'right';
	        }

	        if (left + width > bounds[2] && tAttachment.left === 'right') {
	          left -= targetWidth;
	          tAttachment.left = 'left';
	        }
	      }

	      if (changeAttachX === 'together') {
	        if (left < bounds[0] && tAttachment.left === 'left') {
	          if (eAttachment.left === 'right') {
	            left += targetWidth;
	            tAttachment.left = 'right';

	            left += width;
	            eAttachment.left = 'left';
	          } else if (eAttachment.left === 'left') {
	            left += targetWidth;
	            tAttachment.left = 'right';

	            left -= width;
	            eAttachment.left = 'right';
	          }
	        } else if (left + width > bounds[2] && tAttachment.left === 'right') {
	          if (eAttachment.left === 'left') {
	            left -= targetWidth;
	            tAttachment.left = 'left';

	            left -= width;
	            eAttachment.left = 'right';
	          } else if (eAttachment.left === 'right') {
	            left -= targetWidth;
	            tAttachment.left = 'left';

	            left += width;
	            eAttachment.left = 'left';
	          }
	        } else if (tAttachment.left === 'center') {
	          if (left + width > bounds[2] && eAttachment.left === 'left') {
	            left -= width;
	            eAttachment.left = 'right';
	          } else if (left < bounds[0] && eAttachment.left === 'right') {
	            left += width;
	            eAttachment.left = 'left';
	          }
	        }
	      }

	      if (changeAttachY === 'element' || changeAttachY === 'both') {
	        if (top < bounds[1] && eAttachment.top === 'bottom') {
	          top += height;
	          eAttachment.top = 'top';
	        }

	        if (top + height > bounds[3] && eAttachment.top === 'top') {
	          top -= height;
	          eAttachment.top = 'bottom';
	        }
	      }

	      if (changeAttachX === 'element' || changeAttachX === 'both') {
	        if (left < bounds[0] && eAttachment.left === 'right') {
	          left += width;
	          eAttachment.left = 'left';
	        }

	        if (left + width > bounds[2] && eAttachment.left === 'left') {
	          left -= width;
	          eAttachment.left = 'right';
	        }
	      }

	      if (typeof pin === 'string') {
	        pin = pin.split(',').map(function (p) {
	          return p.trim();
	        });
	      } else if (pin === true) {
	        pin = ['top', 'left', 'right', 'bottom'];
	      }

	      pin = pin || [];

	      var pinned = [];
	      var oob = [];

	      if (top < bounds[1]) {
	        if (pin.indexOf('top') >= 0) {
	          top = bounds[1];
	          pinned.push('top');
	        } else {
	          oob.push('top');
	        }
	      }

	      if (top + height > bounds[3]) {
	        if (pin.indexOf('bottom') >= 0) {
	          top = bounds[3] - height;
	          pinned.push('bottom');
	        } else {
	          oob.push('bottom');
	        }
	      }

	      if (left < bounds[0]) {
	        if (pin.indexOf('left') >= 0) {
	          left = bounds[0];
	          pinned.push('left');
	        } else {
	          oob.push('left');
	        }
	      }

	      if (left + width > bounds[2]) {
	        if (pin.indexOf('right') >= 0) {
	          left = bounds[2] - width;
	          pinned.push('right');
	        } else {
	          oob.push('right');
	        }
	      }

	      if (pinned.length) {
	        (function () {
	          var pinnedClass = undefined;
	          if (typeof _this.options.pinnedClass !== 'undefined') {
	            pinnedClass = _this.options.pinnedClass;
	          } else {
	            pinnedClass = _this.getClass('pinned');
	          }

	          addClasses.push(pinnedClass);
	          pinned.forEach(function (side) {
	            addClasses.push(pinnedClass + '-' + side);
	          });
	        })();
	      }

	      if (oob.length) {
	        (function () {
	          var oobClass = undefined;
	          if (typeof _this.options.outOfBoundsClass !== 'undefined') {
	            oobClass = _this.options.outOfBoundsClass;
	          } else {
	            oobClass = _this.getClass('out-of-bounds');
	          }

	          addClasses.push(oobClass);
	          oob.forEach(function (side) {
	            addClasses.push(oobClass + '-' + side);
	          });
	        })();
	      }

	      if (pinned.indexOf('left') >= 0 || pinned.indexOf('right') >= 0) {
	        eAttachment.left = tAttachment.left = false;
	      }
	      if (pinned.indexOf('top') >= 0 || pinned.indexOf('bottom') >= 0) {
	        eAttachment.top = tAttachment.top = false;
	      }

	      if (tAttachment.top !== targetAttachment.top || tAttachment.left !== targetAttachment.left || eAttachment.top !== _this.attachment.top || eAttachment.left !== _this.attachment.left) {
	        _this.updateAttachClasses(eAttachment, tAttachment);
	      }
	    });

	    defer(function () {
	      if (!(_this.options.addTargetClasses === false)) {
	        updateClasses(_this.target, addClasses, allClasses);
	      }
	      updateClasses(_this.element, addClasses, allClasses);
	    });

	    return { top: top, left: left };
	  }
	});
	/* globals TetherBase */

	'use strict';

	var _TetherBase$Utils = TetherBase.Utils;
	var getBounds = _TetherBase$Utils.getBounds;
	var updateClasses = _TetherBase$Utils.updateClasses;
	var defer = _TetherBase$Utils.defer;

	TetherBase.modules.push({
	  position: function position(_ref) {
	    var _this = this;

	    var top = _ref.top;
	    var left = _ref.left;

	    var _cache = this.cache('element-bounds', function () {
	      return getBounds(_this.element);
	    });

	    var height = _cache.height;
	    var width = _cache.width;

	    var targetPos = this.getTargetBounds();

	    var bottom = top + height;
	    var right = left + width;

	    var abutted = [];
	    if (top <= targetPos.bottom && bottom >= targetPos.top) {
	      ['left', 'right'].forEach(function (side) {
	        var targetPosSide = targetPos[side];
	        if (targetPosSide === left || targetPosSide === right) {
	          abutted.push(side);
	        }
	      });
	    }

	    if (left <= targetPos.right && right >= targetPos.left) {
	      ['top', 'bottom'].forEach(function (side) {
	        var targetPosSide = targetPos[side];
	        if (targetPosSide === top || targetPosSide === bottom) {
	          abutted.push(side);
	        }
	      });
	    }

	    var allClasses = [];
	    var addClasses = [];

	    var sides = ['left', 'top', 'right', 'bottom'];
	    allClasses.push(this.getClass('abutted'));
	    sides.forEach(function (side) {
	      allClasses.push(_this.getClass('abutted') + '-' + side);
	    });

	    if (abutted.length) {
	      addClasses.push(this.getClass('abutted'));
	    }

	    abutted.forEach(function (side) {
	      addClasses.push(_this.getClass('abutted') + '-' + side);
	    });

	    defer(function () {
	      if (!(_this.options.addTargetClasses === false)) {
	        updateClasses(_this.target, addClasses, allClasses);
	      }
	      updateClasses(_this.element, addClasses, allClasses);
	    });

	    return true;
	  }
	});
	/* globals TetherBase */

	'use strict';

	var _slicedToArray = (function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i['return']) _i['return'](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError('Invalid attempt to destructure non-iterable instance'); } }; })();

	TetherBase.modules.push({
	  position: function position(_ref) {
	    var top = _ref.top;
	    var left = _ref.left;

	    if (!this.options.shift) {
	      return;
	    }

	    var shift = this.options.shift;
	    if (typeof this.options.shift === 'function') {
	      shift = this.options.shift.call(this, { top: top, left: left });
	    }

	    var shiftTop = undefined,
	        shiftLeft = undefined;
	    if (typeof shift === 'string') {
	      shift = shift.split(' ');
	      shift[1] = shift[1] || shift[0];

	      var _shift = shift;

	      var _shift2 = _slicedToArray(_shift, 2);

	      shiftTop = _shift2[0];
	      shiftLeft = _shift2[1];

	      shiftTop = parseFloat(shiftTop, 10);
	      shiftLeft = parseFloat(shiftLeft, 10);
	    } else {
	      shiftTop = shift.top;
	      shiftLeft = shift.left;
	    }

	    top += shiftTop;
	    left += shiftLeft;

	    return { top: top, left: left };
	  }
	});
	return Tether;

	}));


/***/ },
/* 96 */,
/* 97 */,
/* 98 */,
/* 99 */,
/* 100 */,
/* 101 */,
/* 102 */,
/* 103 */,
/* 104 */,
/* 105 */,
/* 106 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var App = __webpack_require__(2);
	var $ = __webpack_require__(6);
	var Radio = __webpack_require__(4);

	/**
	 * Toggles legacy server support
	 */
	var EmulateHTTP = Behavior.extend({

	  ui: {
	    toggle : '*[data-action^="legacy-"]'
	  },

	  events: {
	    'click @ui.toggle' : 'toggle'
	  },

	  toggle: function(e) {
	    e.preventDefault();

	    var ajaxurl = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'ajaxurl'
	    });

	    var nonce = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'nonce'
	    });

	    $.getJSON( ajaxurl, {
	      action: 'wc_pos_toggle_legacy_server',
	      enable: $(e.target).data('action').split('-').pop() === 'enable',
	      security: nonce
	    }, function(){
	      window.location.reload();
	    });
	  }

	});

	module.exports = EmulateHTTP;
	App.prototype.set('Behaviors.EmulateHTTP', EmulateHTTP);

/***/ },
/* 107 */,
/* 108 */,
/* 109 */,
/* 110 */,
/* 111 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);
	var hbs = __webpack_require__(7);
	var accounting = __webpack_require__(9);
	var moment = __webpack_require__(27);
	var Utils = __webpack_require__(8);
	var App = __webpack_require__(2);

	/**
	 * is, compare helpers taken from
	 * https://github.com/assemble/handlebars-helpers
	 */

	hbs.registerHelper('is', function (value, test, options) {
	  if ( value && _.includes(test.split('|'), value) ) {
	    return options.fn(this);
	  } else {
	    return options.inverse(this);
	  }
	});

	/*jshint -W071, -W074: suppress warnings  */
	hbs.registerHelper('compare', function(left, operator, right, options) {

	  if (arguments.length < 3) {
	    throw new Error('Handlebars Helper "compare" needs 2 parameters');
	  }

	  if (options === undefined) {
	    options = right;
	    right = operator;
	    operator = '===';
	  }

	  var operators = {
	    //'==': function(l, r) {
	    //  return l == r;
	    //},
	    '===': function(l, r) {
	      return l === r;
	    },
	    //'!=': function(l, r) {
	    //  return l != r;
	    //},
	    '!==': function(l, r) {
	      return l !== r;
	    },
	    '<': function(l, r) {
	      return l < r;
	    },
	    '>': function(l, r) {
	      return l > r;
	    },
	    '<=': function(l, r) {
	      return l <= r;
	    },
	    '>=': function(l, r) {
	      return l >= r;
	    }
	    //'typeof': function(l, r) {
	    //  return typeof l == r;
	    //}
	  };

	  if (!operators[operator]) {
	    throw new Error(
	      'Handlebars Helper "compare" doesn\'t know the operator ' + operator
	    );
	  }

	  var result = operators[operator](left, right);

	  if (result) {
	    return options.fn(this);
	  } else {
	    return options.inverse(this);
	  }
	});
	/*jshint +W071, +W074 */

	hbs.registerHelper('list', function(items, sep, options) {
	  if( _.isArray(items) || _.isObject(items) ){
	    var list = _.map(items, options.fn);
	    return list.join(sep);
	  }
	  return options.fn(items);
	});

	hbs.registerHelper('csv', function(items, options) {
	  return options.fn(items.join(', '));
	});

	hbs.registerHelper('money', function(num, options){
	  var defaultPrecision = accounting.settings.currency.precision,
	      precision = options.hash.precision || defaultPrecision;

	  if( precision === 'auto' ) {
	    precision = Utils.decimalPlaces(num);
	  }

	  // round the number to even
	  num = Utils.round(num, precision);

	  if(options.hash.negative) {
	    num = num * -1;
	  }

	  return accounting.formatMoney(num);
	});

	hbs.registerHelper('number', function(num, options){
	  var defaultPrecision = accounting.settings.number.precision,
	      precision = options.hash.precision || defaultPrecision;

	  if( precision === 'auto' ) {
	    precision = Utils.decimalPlaces(num);
	  }

	  if(options.hash.negative) {
	    num = num * -1;
	  }

	  return accounting.formatNumber(num, precision);
	});

	hbs.registerHelper('formatAddress', function(a, options){
	  a = a || {};

	  var format = [
	    [a.first_name, a.last_name],
	    [a.company],
	    [a.address_1],
	    [a.address_2],
	    [a.city, a.state, a.postcode]
	  ];

	  // format address
	  var address = _.chain(format)
	    .map(function(line) { return _.compact(line).join(' '); })
	    .compact()
	    .join('<br>\n')
	    .value();

	  // prepend title
	  if( address !== '' && options.hash.title ) {
	    address = '<h3>' + options.hash.title + '</h3>\n' + address;
	  }

	  return new hbs.SafeString(address);
	});

	hbs.registerHelper('formatDate', function(date, options){
	  var f = options.hash.format || '';
	  return moment(date).format(f);
	});

	hbs.registerHelper('formatDay', function(day, options){
	  var f = options.hash.format || '';
	  var idx = parseInt(day, 10) + 1;
	  return moment().isoWeekday(idx).format(f);
	});

	hbs.registerHelper('debug', function(optionalValue) {
	  console.log('Current Context');
	  console.log('====================');
	  console.log(this);

	  if (optionalValue) {
	    console.log('Value');
	    console.log('====================');
	    console.log(optionalValue);
	  }
	});

	hbs.registerHelper('formatCustomerName', function(customer) {
	  var name = _(customer).pick(['first_name','last_name'])
	    .values()
	    .map(function( value ){
	      return value.trim();
	    })
	    .compact()
	    .value()
	    .join(' ');

	  if( customer && !name ){
	    name = customer.username;
	  }

	  return name;
	});

	hbs.registerHelper('namespace', function(str){
	  return App.prototype.namespace(str);
	});

	//hbs.registerHelper('getOption', function(key){
	//  var lookup = key.split('.');
	//  var option = Radio.request( 'entities', 'get', {
	//    type: 'option',
	//    name: lookup.shift()
	//  });
	//  for(var i = 0; i < lookup.length; i++) {
	//    option = option[lookup[i]];
	//  }
	//  return option;
	//});

/***/ },
/* 112 */
/***/ function(module, exports, __webpack_require__) {

	var Application = __webpack_require__(2);
	var bb = __webpack_require__(16);
	var LayoutView = __webpack_require__(113);
	var debug = __webpack_require__(13)('app');

	module.exports = Application.extend({

	  initialize: function() {
	    // init Root LayoutView
	    this.layout = new LayoutView();
	    this.layout.render();
	  },

	  /**
	   * Set up application with start params
	   */
	  onBeforeStart: function(){
	    // debugging
	    debug( 'starting WooCommerce POS app' );

	    // start header service
	    this.headerService.start();
	  },

	  onStart: function(){
	    bb.history.start();
	  }

	});

/***/ },
/* 113 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var $ = __webpack_require__(6);
	var Radio = __webpack_require__(4);
	var globalChannel = Radio.channel('global');

	module.exports = LayoutView.extend({
	  el: '#page',

	  template: function(){
	    return '' +
	      '<header id="header"></header>' +
	      '<div id="menu"></div>' +
	      '<div id="tabs" class="tabs"></div>' +
	      '<main id="main"></main>' +
	      '<div id="modal"></div>';
	  },

	  regions: {
	    header: '#header',
	    menu  : '#menu',
	    tabs  : '#tabs',
	    main  : '#main',
	    modal : '#modal'
	  },

	  initialize: function(){
	    this.getRegion('main').on('show', this.setup, this);
	    globalChannel.on('tab:label', this.updateTabLabel, this);
	  },

	  setup: function(layout){
	    if(layout.columns && layout.columns === 2){
	      this.$el.addClass('two-column');
	      this.showTabs();
	    } else {
	      this.$el.removeClass('two-column');
	      this.getRegion('tabs').empty();
	    }
	  },

	  showTabs: function(){
	    var tabs = this.getRegion('main').tabs = Radio.request('tabs', 'view', {
	      tabs: [
	        {id: 'left'},
	        {id: 'right'}
	      ]
	    });
	    this.listenTo(tabs.collection, 'active:tab', this.toggleLayout);
	    this.getRegion('tabs').show(tabs);
	  },

	  toggleLayout: function(model){
	    $('#main').removeClass('left-active right-active');
	    $('#main').addClass(model.id + '-active');
	  },

	  updateTabLabel: function(options){
	    this.getRegion('main').tabs.setLabel(options);
	  }

	});

/***/ },
/* 114 */
/***/ function(module, exports, __webpack_require__) {

	// some nice features may be:
	// - queue which prioritises tasks
	// - retry for server timeouts

	// DO NOT include this file in wp-admin
	// changes are made to the global $.ajax & bb.sync

	var bb = __webpack_require__(16);
	var Radio = __webpack_require__(4);
	bb.idbSync = __webpack_require__(115);
	bb.ajaxSync = bb.sync;

	// set jquery ajax globals
	bb.$.ajaxSetup({
	  data: {
	    security: function(){
	      return Radio.request('entities', 'get', {
	        type: 'option',
	        name: 'nonce'
	      });
	    }
	  },
	  // for straight jquery ajax calls
	  beforeSend: function(xhr){
	    xhr.setRequestHeader('X-WC-POS', 1);
	  },
	  timeout: 50000 // 50 seconds
	});

	// global ajax error handler
	bb.$( document ).ajaxError(function( event, jqXHR, ajaxSettings, thrownError ) {
	  Radio.trigger('global', 'error', {
	    jqXHR       : jqXHR,
	    thrownError : thrownError
	  });
	});

	// override Backbone.sync
	bb.sync = function(method, entity, options) {
	  // idb
	  if(!options.remote &&
	    (entity.db || (entity.collection && entity.collection.db))){
	    return bb.idbSync.apply(this, [method, entity, options]);
	  }
	  // server
	  options.beforeSend = function(xhr){
	    xhr.setRequestHeader('X-WC-POS', 1);
	  };
	  return bb.ajaxSync.apply(this, [method, entity, options]);
	};

/***/ },
/* 115 */
/***/ function(module, exports, __webpack_require__) {

	var $ = __webpack_require__(6);
	var noop = function(){};

	var methods = {
	  'read': function(model, options, db){
	    if(model.id){
	      return db.read(model, options);
	    }
	    return db.getAll(options);
	  },
	  'create': function(model, options, db){
	    if (model.id) {
	      return db.update(model, options);
	    }
	    return db.create(model, options);
	  },
	  'update': function(model, options, db){
	    if (model.id) {
	      return db.update(model, options);
	    }
	    return db.create(model, options);
	  },
	  'delete': function(model, options, db){
	    if (model.id) {
	      return db.destroy(model, options);
	    }
	  }
	};

	var sync = function(method, entity, options) {
	  var deferred = new $.Deferred(),
	      db = entity.db || entity.collection.db,
	      success = options.success || noop,
	      error = options.success || noop;

	  options.success = function (result) {
	    success.apply(this, arguments);
	    deferred.resolve(result);
	  };

	  options.error = function (result) {
	    error.apply(this, arguments);
	    deferred.reject(result);
	  };

	  db.open()
	    .then(function(){
	      if(methods[method]){
	        return methods[method](entity, options, db);
	      }
	    });

	  return deferred.promise();
	};

	module.exports = sync;

/***/ },
/* 116 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var Service = __webpack_require__(20);
	var TitleBar = __webpack_require__(117);
	var Menu = __webpack_require__(154);
	var App = __webpack_require__(2);
	var Radio = __webpack_require__(4);
	var routerChannel = Radio.channel('router');
	var BrowserModal = __webpack_require__(156);
	var _ = __webpack_require__(5);
	var Modernizr = global['Modernizr'];

	var Service = Service.extend({
	  channelName: 'header',

	  initialize: function(options) {
	    options = options || {};
	    this.header = options.headerContainer;
	    this.menu = options.menuContainer;

	    this.listenToOnce(routerChannel, {
	      'route': this.browserCheck
	    });

	    this.channel.reply({
	      'update:title': this.updateTitle
	    }, this);
	  },

	  onStart: function(){
	    this.showTitleBar();
	    this.showMenu();
	  },

	  showTitleBar: function(){
	    var view = new TitleBar();
	    this.header.show(view);
	  },

	  updateTitle: function(title){
	    this.header.currentView.update(title);
	  },

	  showMenu: function(){
	    var view = new Menu();

	    this.channel.reply({
	      'open:menu'   : view.open,
	      'close:menu'  : view.close
	    }, view);

	    this.menu.show(view);
	  },

	  browserCheck: function(){
	    // smil targets all IE: http://caniuse.com/#feat=svg-smil
	    var props = ['flexbox', 'indexeddb', 'smil'],
	        pass = _.every(props, function(prop){ return Modernizr[prop]; });

	    if(!pass){
	      var view = new BrowserModal();
	      Radio.request('modal', 'open', view);
	    }
	  },

	  onStop: function(){
	    this.channel.reset();
	  }
	});

	module.exports = Service;
	App.prototype.set('HeaderApp.Service', Service);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 117 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var HotKeys = __webpack_require__(118);
	var Dropdown = __webpack_require__(152);
	var Radio = __webpack_require__(4);
	var HelpModal = __webpack_require__(153);

	var View = ItemView.extend({
	  template: 'header',

	  initialize: function(){
	    var store = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'store'
	    });
	    this.storeName = store.name;
	  },

	  templateHelpers: function(){
	    return {
	      name: this.storeName
	    };
	  },

	  ui: {
	    'menu': '#menu-btn'
	  },

	  events: {
	    'click @ui.menu': 'openMenu'
	  },

	  keyEvents: {
	    'help': 'showHelpModal'
	  },

	  behaviors: {
	    HotKeys: {
	      behaviorClass: HotKeys
	    },
	    Dropdown: {
	      behaviorClass: Dropdown,
	      position: 'bottom right'
	      //tetherOptions: {
	      //  attachment: 'top right',
	      //  targetAttachment: 'bottom right'
	      //}
	    }
	  },

	  update: function(str){
	    var title = str ? str : this.storeName;
	    this.$('h1').text(title);
	  },

	  openMenu: function(e){
	    e.preventDefault();
	    Radio.request('header', 'open:menu');
	  },

	  showHelpModal: function() {
	    var view = new HelpModal();
	    Radio.request('modal', 'open', view);
	  }

	});

	module.exports = View;
	App.prototype.set('HeaderApp.Views.TitleBar', View);

/***/ },
/* 118 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var Radio = __webpack_require__(4);
	var _ = __webpack_require__(5);
	var debug = __webpack_require__(13)('hotkey');
	var App = __webpack_require__(2);
	var Combokeys = __webpack_require__(119);

	var HotKeys = Behavior.extend({
	  keys: [],

	  initialize: function(options) {
	    options = options || {};
	    this.element = options.el;
	    this.hotkeys = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'hotkeys'
	    }) || {};
	  },

	  // todo: this.element => this.view.el
	  // todo: refactor!
	  onRender: function(){
	    var element = this.element || document.documentElement,
	        _view = this.view;

	    this.combokeys = new Combokeys(element);

	    _.each( _view.keyEvents, function(callback, id) {
	      var trigger = this.hotkeys[id];

	      if (!_.isFunction(callback)) { callback = _view[callback]; }

	      if(!trigger || !callback){
	        return debug('hotkey not found: ' + id, this.view.keyEvents);
	      }

	      this.combokeys.bind(trigger.key, function(e, combo){
	        callback.call(_view, e, combo);
	      });

	      this.keys.push(trigger.key);

	    }, this);
	  },

	  onDestroy: function(){
	    _.each( this.keys, function( key ) {
	      this.combokeys.unbind(key);
	    }, this);
	  }

	});

	module.exports = HotKeys;
	App.prototype.set('Behaviors.HotKeys', HotKeys);

/***/ },
/* 119 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	module.exports = function (element) {
	  var self = this
	  var Combokeys = self.constructor

	  /**
	   * a list of all the callbacks setup via Combokeys.bind()
	   *
	   * @type {Object}
	   */
	  self.callbacks = {}

	  /**
	   * direct map of string combinations to callbacks used for trigger()
	   *
	   * @type {Object}
	   */
	  self.directMap = {}

	  /**
	   * keeps track of what level each sequence is at since multiple
	   * sequences can start out with the same sequence
	   *
	   * @type {Object}
	   */
	  self.sequenceLevels = {}

	  /**
	   * variable to store the setTimeout call
	   *
	   * @type {null|number}
	   */
	  self.resetTimer

	  /**
	   * temporary state where we will ignore the next keyup
	   *
	   * @type {boolean|string}
	   */
	  self.ignoreNextKeyup = false

	  /**
	   * temporary state where we will ignore the next keypress
	   *
	   * @type {boolean}
	   */
	  self.ignoreNextKeypress = false

	  /**
	   * are we currently inside of a sequence?
	   * type of action ("keyup" or "keydown" or "keypress") or false
	   *
	   * @type {boolean|string}
	   */
	  self.nextExpectedAction = false

	  self.element = element

	  self.addEvents()

	  Combokeys.instances.push(self)
	  return self
	}

	module.exports.prototype.bind = __webpack_require__(120)
	module.exports.prototype.bindMultiple = __webpack_require__(121)
	module.exports.prototype.unbind = __webpack_require__(122)
	module.exports.prototype.trigger = __webpack_require__(123)
	module.exports.prototype.reset = __webpack_require__(124)
	module.exports.prototype.stopCallback = __webpack_require__(125)
	module.exports.prototype.handleKey = __webpack_require__(126)
	module.exports.prototype.addEvents = __webpack_require__(128)
	module.exports.prototype.bindSingle = __webpack_require__(135)
	module.exports.prototype.getKeyInfo = __webpack_require__(136)
	module.exports.prototype.pickBestAction = __webpack_require__(140)
	module.exports.prototype.getReverseMap = __webpack_require__(141)
	module.exports.prototype.getMatches = __webpack_require__(142)
	module.exports.prototype.resetSequences = __webpack_require__(144)
	module.exports.prototype.fireCallback = __webpack_require__(145)
	module.exports.prototype.bindSequence = __webpack_require__(148)
	module.exports.prototype.resetSequenceTimer = __webpack_require__(149)
	module.exports.prototype.detach = __webpack_require__(150)

	module.exports.instances = []
	module.exports.reset = __webpack_require__(151)

	/**
	 * variable to store the flipped version of MAP from above
	 * needed to check if we should use keypress or not when no action
	 * is specified
	 *
	 * @type {Object|undefined}
	 */
	module.exports.REVERSE_MAP = null


/***/ },
/* 120 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * binds an event to Combokeys
	 *
	 * can be a single key, a combination of keys separated with +,
	 * an array of keys, or a sequence of keys separated by spaces
	 *
	 * be sure to list the modifier keys first to make sure that the
	 * correct key ends up getting bound (the last key in the pattern)
	 *
	 * @param {string|Array} keys
	 * @param {Function} callback
	 * @param {string=} action - "keypress", "keydown", or "keyup"
	 * @returns void
	 */
	module.exports = function (keys, callback, action) {
	  var self = this

	  keys = keys instanceof Array ? keys : [keys]
	  self.bindMultiple(keys, callback, action)
	  return self
	}


/***/ },
/* 121 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * binds multiple combinations to the same callback
	 *
	 * @param {Array} combinations
	 * @param {Function} callback
	 * @param {string|undefined} action
	 * @returns void
	 */
	module.exports = function (combinations, callback, action) {
	  var self = this

	  for (var j = 0; j < combinations.length; ++j) {
	    self.bindSingle(combinations[j], callback, action)
	  }
	}


/***/ },
/* 122 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * unbinds an event to Combokeys
	 *
	 * the unbinding sets the callback function of the specified key combo
	 * to an empty function and deletes the corresponding key in the
	 * directMap dict.
	 *
	 * TODO: actually remove this from the callbacks dictionary instead
	 * of binding an empty function
	 *
	 * the keycombo+action has to be exactly the same as
	 * it was defined in the bind method
	 *
	 * @param {string|Array} keys
	 * @param {string} action
	 * @returns void
	 */
	module.exports = function (keys, action) {
	  var self = this

	  return self.bind(keys, function () {}, action)
	}


/***/ },
/* 123 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * triggers an event that has already been bound
	 *
	 * @param {string} keys
	 * @param {string=} action
	 * @returns void
	 */
	module.exports = function (keys, action) {
	  var self = this
	  if (self.directMap[keys + ':' + action]) {
	    self.directMap[keys + ':' + action]({}, keys)
	  }
	  return this
	}


/***/ },
/* 124 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * resets the library back to its initial state. This is useful
	 * if you want to clear out the current keyboard shortcuts and bind
	 * new ones - for example if you switch to another page
	 *
	 * @returns void
	 */
	module.exports = function () {
	  var self = this
	  self.callbacks = {}
	  self.directMap = {}
	  return this
	}


/***/ },
/* 125 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	* should we stop this event before firing off callbacks
	*
	* @param {Event} e
	* @param {Element} element
	* @return {boolean}
	*/
	module.exports = function (e, element) {
	  // if the element has the class "combokeys" then no need to stop
	  if ((' ' + element.className + ' ').indexOf(' combokeys ') > -1) {
	    return false
	  }

	  var tagName = element.tagName.toLowerCase()

	  // stop for input, select, and textarea
	  return tagName === 'input' || tagName === 'select' || tagName === 'textarea' || element.isContentEditable
	}


/***/ },
/* 126 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * handles a character key event
	 *
	 * @param {string} character
	 * @param {Array} modifiers
	 * @param {Event} e
	 * @returns void
	 */
	module.exports = function (character, modifiers, e) {
	  var self = this
	  var callbacks
	  var j
	  var doNotReset = {}
	  var maxLevel = 0
	  var processedSequenceCallback = false
	  var isModifier
	  var ignoreThisKeypress

	  callbacks = self.getMatches(character, modifiers, e)
	  // Calculate the maxLevel for sequences so we can only execute the longest callback sequence
	  for (j = 0; j < callbacks.length; ++j) {
	    if (callbacks[j].seq) {
	      maxLevel = Math.max(maxLevel, callbacks[j].level)
	    }
	  }

	  // loop through matching callbacks for this key event
	  for (j = 0; j < callbacks.length; ++j) {
	    // fire for all sequence callbacks
	    // this is because if for example you have multiple sequences
	    // bound such as "g i" and "g t" they both need to fire the
	    // callback for matching g cause otherwise you can only ever
	    // match the first one
	    if (callbacks[j].seq) {
	      // only fire callbacks for the maxLevel to prevent
	      // subsequences from also firing
	      //
	      // for example 'a option b' should not cause 'option b' to fire
	      // even though 'option b' is part of the other sequence
	      //
	      // any sequences that do not match here will be discarded
	      // below by the resetSequences call
	      if (callbacks[j].level !== maxLevel) {
	        continue
	      }

	      processedSequenceCallback = true

	      // keep a list of which sequences were matches for later
	      doNotReset[callbacks[j].seq] = 1
	      self.fireCallback(callbacks[j].callback, e, callbacks[j].combo, callbacks[j].seq)
	      continue
	    }

	    // if there were no sequence matches but we are still here
	    // that means this is a regular match so we should fire that
	    if (!processedSequenceCallback) {
	      self.fireCallback(callbacks[j].callback, e, callbacks[j].combo)
	    }
	  }

	  // if the key you pressed matches the type of sequence without
	  // being a modifier (ie "keyup" or "keypress") then we should
	  // reset all sequences that were not matched by this event
	  //
	  // this is so, for example, if you have the sequence "h a t" and you
	  // type "h e a r t" it does not match.  in this case the "e" will
	  // cause the sequence to reset
	  //
	  // modifier keys are ignored because you can have a sequence
	  // that contains modifiers such as "enter ctrl+space" and in most
	  // cases the modifier key will be pressed before the next key
	  //
	  // also if you have a sequence such as "ctrl+b a" then pressing the
	  // "b" key will trigger a "keypress" and a "keydown"
	  //
	  // the "keydown" is expected when there is a modifier, but the
	  // "keypress" ends up matching the nextExpectedAction since it occurs
	  // after and that causes the sequence to reset
	  //
	  // we ignore keypresses in a sequence that directly follow a keydown
	  // for the same character
	  ignoreThisKeypress = e.type === 'keypress' && self.ignoreNextKeypress
	  isModifier = __webpack_require__(127)
	  if (e.type === self.nextExpectedAction && !isModifier(character) && !ignoreThisKeypress) {
	    self.resetSequences(doNotReset)
	  }

	  self.ignoreNextKeypress = processedSequenceCallback && e.type === 'keydown'
	}


/***/ },
/* 127 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * determines if the keycode specified is a modifier key or not
	 *
	 * @param {string} key
	 * @returns {boolean}
	 */
	module.exports = function (key) {
	  return key === 'shift' || key === 'ctrl' || key === 'alt' || key === 'meta'
	}


/***/ },
/* 128 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'
	module.exports = function () {
	  var self = this
	  var on = __webpack_require__(129)
	  var element = self.element

	  self.eventHandler = __webpack_require__(130).bind(self)

	  on(element, 'keypress', self.eventHandler)
	  on(element, 'keydown', self.eventHandler)
	  on(element, 'keyup', self.eventHandler)
	}


/***/ },
/* 129 */
/***/ function(module, exports) {

	module.exports = on;
	module.exports.on = on;
	module.exports.off = off;

	function on (element, event, callback, capture) {
	  !element.addEventListener && (event = 'on' + event);
	  (element.addEventListener || element.attachEvent).call(element, event, callback, capture);
	  return callback;
	}

	function off (element, event, callback, capture) {
	  !element.removeEventListener && (event = 'on' + event);
	  (element.removeEventListener || element.detachEvent).call(element, event, callback, capture);
	  return callback;
	}


/***/ },
/* 130 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * handles a keydown event
	 *
	 * @param {Event} e
	 * @returns void
	 */
	module.exports = function (e) {
	  var self = this
	  var characterFromEvent
	  var eventModifiers

	  // normalize e.which for key events
	  // @see http://stackoverflow.com/questions/4285627/javascript-keycode-vs-charcode-utter-confusion
	  if (typeof e.which !== 'number') {
	    e.which = e.keyCode
	  }
	  characterFromEvent = __webpack_require__(131)
	  var character = characterFromEvent(e)

	  // no character found then stop
	  if (!character) {
	    return
	  }

	  // need to use === for the character check because the character can be 0
	  if (e.type === 'keyup' && self.ignoreNextKeyup === character) {
	    self.ignoreNextKeyup = false
	    return
	  }

	  eventModifiers = __webpack_require__(134)
	  self.handleKey(character, eventModifiers(e), e)
	}


/***/ },
/* 131 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * takes the event and returns the key character
	 *
	 * @param {Event} e
	 * @return {string}
	 */
	module.exports = function (e) {
	  var SPECIAL_KEYS_MAP,
	  SPECIAL_CHARACTERS_MAP
	  SPECIAL_KEYS_MAP = __webpack_require__(132)
	  SPECIAL_CHARACTERS_MAP = __webpack_require__(133)

	  // for keypress events we should return the character as is
	  if (e.type === 'keypress') {
	    var character = String.fromCharCode(e.which)

	    // if the shift key is not pressed then it is safe to assume
	    // that we want the character to be lowercase.  this means if
	    // you accidentally have caps lock on then your key bindings
	    // will continue to work
	    //
	    // the only side effect that might not be desired is if you
	    // bind something like 'A' cause you want to trigger an
	    // event when capital A is pressed caps lock will no longer
	    // trigger the event.  shift+a will though.
	    if (!e.shiftKey) {
	      character = character.toLowerCase()
	    }

	    return character
	  }

	  // for non keypress events the special maps are needed
	  if (SPECIAL_KEYS_MAP[e.which]) {
	    return SPECIAL_KEYS_MAP[e.which]
	  }

	  if (SPECIAL_CHARACTERS_MAP[e.which]) {
	    return SPECIAL_CHARACTERS_MAP[e.which]
	  }

	  // if it is not in the special map

	  // with keydown and keyup events the character seems to always
	  // come in as an uppercase character whether you are pressing shift
	  // or not.  we should make sure it is always lowercase for comparisons
	  return String.fromCharCode(e.which).toLowerCase()
	}


/***/ },
/* 132 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * mapping of special keycodes to their corresponding keys
	 *
	 * everything in this dictionary cannot use keypress events
	 * so it has to be here to map to the correct keycodes for
	 * keyup/keydown events
	 *
	 * @type {Object}
	 */
	module.exports = {
	  8: 'backspace',
	  9: 'tab',
	  13: 'enter',
	  16: 'shift',
	  17: 'ctrl',
	  18: 'alt',
	  20: 'capslock',
	  27: 'esc',
	  32: 'space',
	  33: 'pageup',
	  34: 'pagedown',
	  35: 'end',
	  36: 'home',
	  37: 'left',
	  38: 'up',
	  39: 'right',
	  40: 'down',
	  45: 'ins',
	  46: 'del',
	  91: 'meta',
	  93: 'meta',
	  187: 'plus',
	  189: 'minus',
	  224: 'meta'
	}

	/**
	 * loop through the f keys, f1 to f19 and add them to the map
	 * programatically
	 */
	for (var i = 1; i < 20; ++i) {
	  module.exports[111 + i] = 'f' + i
	}

	/**
	 * loop through to map numbers on the numeric keypad
	 */
	for (i = 0; i <= 9; ++i) {
	  module.exports[i + 96] = i
	}


/***/ },
/* 133 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * mapping for special characters so they can support
	 *
	 * this dictionary is only used incase you want to bind a
	 * keyup or keydown event to one of these keys
	 *
	 * @type {Object}
	 */
	module.exports = {
	  106: '*',
	  107: '+',
	  109: '-',
	  110: '.',
	  111: '/',
	  186: ';',
	  187: '=',
	  188: ',',
	  189: '-',
	  190: '.',
	  191: '/',
	  192: '`',
	  219: '[',
	  220: '\\',
	  221: ']',
	  222: "'"
	}


/***/ },
/* 134 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * takes a key event and figures out what the modifiers are
	 *
	 * @param {Event} e
	 * @returns {Array}
	 */
	module.exports = function (e) {
	  var modifiers = []

	  if (e.shiftKey) {
	    modifiers.push('shift')
	  }

	  if (e.altKey) {
	    modifiers.push('alt')
	  }

	  if (e.ctrlKey) {
	    modifiers.push('ctrl')
	  }

	  if (e.metaKey) {
	    modifiers.push('meta')
	  }

	  return modifiers
	}


/***/ },
/* 135 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * binds a single keyboard combination
	 *
	 * @param {string} combination
	 * @param {Function} callback
	 * @param {string=} action
	 * @param {string=} sequenceName - name of sequence if part of sequence
	 * @param {number=} level - what part of the sequence the command is
	 * @returns void
	 */
	module.exports = function (combination, callback, action, sequenceName, level) {
	  var self = this

	  // store a direct mapped reference for use with Combokeys.trigger
	  self.directMap[combination + ':' + action] = callback

	  // make sure multiple spaces in a row become a single space
	  combination = combination.replace(/\s+/g, ' ')

	  var sequence = combination.split(' ')
	  var info

	  // if this pattern is a sequence of keys then run through this method
	  // to reprocess each pattern one key at a time
	  if (sequence.length > 1) {
	    self.bindSequence(combination, sequence, callback, action)
	    return
	  }

	  info = self.getKeyInfo(combination, action)

	  // make sure to initialize array if this is the first time
	  // a callback is added for this key
	  self.callbacks[info.key] = self.callbacks[info.key] || []

	  // remove an existing match if there is one
	  self.getMatches(info.key, info.modifiers, {type: info.action}, sequenceName, combination, level)

	  // add this call back to the array
	  // if it is a sequence put it at the beginning
	  // if not put it at the end
	  //
	  // this is important because the way these are processed expects
	  // the sequence ones to come first
	  self.callbacks[info.key][sequenceName ? 'unshift' : 'push']({
	    callback: callback,
	    modifiers: info.modifiers,
	    action: info.action,
	    seq: sequenceName,
	    level: level,
	    combo: combination
	  })
	}


/***/ },
/* 136 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * Gets info for a specific key combination
	 *
	 * @param  {string} combination key combination ("command+s" or "a" or "*")
	 * @param  {string=} action
	 * @returns {Object}
	 */
	module.exports = function (combination, action) {
	  var self = this
	  var keysFromString
	  var keys
	  var key
	  var j
	  var modifiers = []
	  var SPECIAL_ALIASES
	  var SHIFT_MAP
	  var isModifier

	  keysFromString = __webpack_require__(137)
	  // take the keys from this pattern and figure out what the actual
	  // pattern is all about
	  keys = keysFromString(combination)

	  SPECIAL_ALIASES = __webpack_require__(138)
	  SHIFT_MAP = __webpack_require__(139)
	  isModifier = __webpack_require__(127)
	  for (j = 0; j < keys.length; ++j) {
	    key = keys[j]

	    // normalize key names
	    if (SPECIAL_ALIASES[key]) {
	      key = SPECIAL_ALIASES[key]
	    }

	    // if this is not a keypress event then we should
	    // be smart about using shift keys
	    // this will only work for US keyboards however
	    if (action && action !== 'keypress' && SHIFT_MAP[key]) {
	      key = SHIFT_MAP[key]
	      modifiers.push('shift')
	    }

	    // if this key is a modifier then add it to the list of modifiers
	    if (isModifier(key)) {
	      modifiers.push(key)
	    }
	  }

	  // depending on what the key combination is
	  // we will try to pick the best event for it
	  action = self.pickBestAction(key, modifiers, action)

	  return {
	    key: key,
	    modifiers: modifiers,
	    action: action
	  }
	}


/***/ },
/* 137 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * Converts from a string key combination to an array
	 *
	 * @param  {string} combination like "command+shift+l"
	 * @return {Array}
	 */
	module.exports = function (combination) {
	  if (combination === '+') {
	    return ['+']
	  }

	  return combination.split('+')
	}


/***/ },
/* 138 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * this is a list of special strings you can use to map
	 * to modifier keys when you specify your keyboard shortcuts
	 *
	 * @type {Object}
	 */
	module.exports = {
	  'option': 'alt',
	  'command': 'meta',
	  'return': 'enter',
	  'escape': 'esc',
	  'mod': /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? 'meta' : 'ctrl'
	}


/***/ },
/* 139 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * this is a mapping of keys that require shift on a US keypad
	 * back to the non shift equivelents
	 *
	 * this is so you can use keyup events with these keys
	 *
	 * note that this will only work reliably on US keyboards
	 *
	 * @type {Object}
	 */
	module.exports = {
	  '~': '`',
	  '!': '1',
	  '@': '2',
	  '#': '3',
	  '$': '4',
	  '%': '5',
	  '^': '6',
	  '&': '7',
	  '*': '8',
	  '(': '9',
	  ')': '0',
	  '_': '-',
	  '+': '=',
	  ':': ';',
	  '"': "'",
	  '<': ',',
	  '>': '.',
	  '?': '/',
	  '|': '\\'
	}


/***/ },
/* 140 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * picks the best action based on the key combination
	 *
	 * @param {string} key - character for key
	 * @param {Array} modifiers
	 * @param {string=} action passed in
	 */
	module.exports = function (key, modifiers, action) {
	  var self = this

	  // if no action was picked in we should try to pick the one
	  // that we think would work best for this key
	  if (!action) {
	    action = self.getReverseMap()[key] ? 'keydown' : 'keypress'
	  }

	  // modifier keys don't work as expected with keypress,
	  // switch to keydown
	  if (action === 'keypress' && modifiers.length) {
	    action = 'keydown'
	  }

	  return action
	}


/***/ },
/* 141 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * reverses the map lookup so that we can look for specific keys
	 * to see what can and can't use keypress
	 *
	 * @return {Object}
	 */
	module.exports = function () {
	  var self = this
	  var constructor = self.constructor
	  var SPECIAL_KEYS_MAP

	  if (!constructor.REVERSE_MAP) {
	    constructor.REVERSE_MAP = {}
	    SPECIAL_KEYS_MAP = __webpack_require__(132)
	    for (var key in SPECIAL_KEYS_MAP) {
	      // pull out the numeric keypad from here cause keypress should
	      // be able to detect the keys from the character
	      if (key > 95 && key < 112) {
	        continue
	      }

	      if (SPECIAL_KEYS_MAP.hasOwnProperty(key)) {
	        constructor.REVERSE_MAP[SPECIAL_KEYS_MAP[key]] = key
	      }
	    }
	  }
	  return constructor.REVERSE_MAP
	}


/***/ },
/* 142 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * finds all callbacks that match based on the keycode, modifiers,
	 * and action
	 *
	 * @param {string} character
	 * @param {Array} modifiers
	 * @param {Event|Object} e
	 * @param {string=} sequenceName - name of the sequence we are looking for
	 * @param {string=} combination
	 * @param {number=} level
	 * @returns {Array}
	 */
	module.exports = function (character, modifiers, e, sequenceName, combination, level) {
	  var self = this
	  var j
	  var callback
	  var matches = []
	  var action = e.type
	  var isModifier
	  var modifiersMatch

	  if (action === 'keypress') {
	    // 'any-character' callbacks are only on `keypress`
	    var anyCharCallbacks = self.callbacks['any-character'] || []
	    anyCharCallbacks.forEach(function (callback) {
	      matches.push(callback)
	    })
	  }

	  if (!self.callbacks[character]) {return matches}

	  isModifier = __webpack_require__(127)
	  // if a modifier key is coming up on its own we should allow it
	  if (action === 'keyup' && isModifier(character)) {
	    modifiers = [character]
	  }

	  // loop through all callbacks for the key that was pressed
	  // and see if any of them match
	  for (j = 0; j < self.callbacks[character].length; ++j) {
	    callback = self.callbacks[character][j]

	    // if a sequence name is not specified, but this is a sequence at
	    // the wrong level then move onto the next match
	    if (!sequenceName && callback.seq && self.sequenceLevels[callback.seq] !== callback.level) {
	      continue
	    }

	    // if the action we are looking for doesn't match the action we got
	    // then we should keep going
	    if (action !== callback.action) {
	      continue
	    }

	    // if this is a keypress event and the meta key and control key
	    // are not pressed that means that we need to only look at the
	    // character, otherwise check the modifiers as well
	    //
	    // chrome will not fire a keypress if meta or control is down
	    // safari will fire a keypress if meta or meta+shift is down
	    // firefox will fire a keypress if meta or control is down
	    modifiersMatch = __webpack_require__(143)
	    if ((action === 'keypress' && !e.metaKey && !e.ctrlKey) || modifiersMatch(modifiers, callback.modifiers)) {
	      // when you bind a combination or sequence a second time it
	      // should overwrite the first one.  if a sequenceName or
	      // combination is specified in this call it does just that
	      //
	      // @todo make deleting its own method?
	      var deleteCombo = !sequenceName && callback.combo === combination
	      var deleteSequence = sequenceName && callback.seq === sequenceName && callback.level === level
	      if (deleteCombo || deleteSequence) {
	        self.callbacks[character].splice(j, 1)
	      }

	      matches.push(callback)
	    }
	  }

	  return matches
	}


/***/ },
/* 143 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * checks if two arrays are equal
	 *
	 * @param {Array} modifiers1
	 * @param {Array} modifiers2
	 * @returns {boolean}
	 */
	module.exports = function (modifiers1, modifiers2) {
	  return modifiers1.sort().join(',') === modifiers2.sort().join(',')
	}


/***/ },
/* 144 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * resets all sequence counters except for the ones passed in
	 *
	 * @param {Object} doNotReset
	 * @returns void
	 */
	module.exports = function (doNotReset) {
	  var self = this

	  doNotReset = doNotReset || {}

	  var activeSequences = false
	  var key

	  for (key in self.sequenceLevels) {
	    if (doNotReset[key]) {
	      activeSequences = true
	      continue
	    }
	    self.sequenceLevels[key] = 0
	  }

	  if (!activeSequences) {
	    self.nextExpectedAction = false
	  }
	}


/***/ },
/* 145 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * actually calls the callback function
	 *
	 * if your callback function returns false this will use the jquery
	 * convention - prevent default and stop propogation on the event
	 *
	 * @param {Function} callback
	 * @param {Event} e
	 * @returns void
	 */
	module.exports = function (callback, e, combo, sequence) {
	  var self = this
	  var preventDefault
	  var stopPropagation

	  // if this event should not happen stop here
	  if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
	    return
	  }

	  if (callback(e, combo) === false) {
	    preventDefault = __webpack_require__(146)
	    preventDefault(e)
	    stopPropagation = __webpack_require__(147)
	    stopPropagation(e)
	  }
	}


/***/ },
/* 146 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * prevents default for this event
	 *
	 * @param {Event} e
	 * @returns void
	 */
	module.exports = function (e) {
	  if (e.preventDefault) {
	    e.preventDefault()
	    return
	  }

	  e.returnValue = false
	}


/***/ },
/* 147 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * stops propogation for this event
	 *
	 * @param {Event} e
	 * @returns void
	 */
	module.exports = function (e) {
	  if (e.stopPropagation) {
	    e.stopPropagation()
	    return
	  }

	  e.cancelBubble = true
	}


/***/ },
/* 148 */
/***/ function(module, exports, __webpack_require__) {

	/* eslint-env node, browser */
	'use strict'

	/**
	 * binds a key sequence to an event
	 *
	 * @param {string} combo - combo specified in bind call
	 * @param {Array} keys
	 * @param {Function} callback
	 * @param {string=} action
	 * @returns void
	 */
	module.exports = function (combo, keys, callback, action) {
	  var self = this

	  // start off by adding a sequence level record for this combination
	  // and setting the level to 0
	  self.sequenceLevels[combo] = 0

	  /**
	   * callback to increase the sequence level for this sequence and reset
	   * all other sequences that were active
	   *
	   * @param {string} nextAction
	   * @returns {Function}
	   */
	  function increaseSequence (nextAction) {
	    return function () {
	      self.nextExpectedAction = nextAction
	      ++self.sequenceLevels[combo]
	      self.resetSequenceTimer()
	    }
	  }

	  /**
	   * wraps the specified callback inside of another function in order
	   * to reset all sequence counters as soon as this sequence is done
	   *
	   * @param {Event} e
	   * @returns void
	   */
	  function callbackAndReset (e) {
	    var characterFromEvent
	    self.fireCallback(callback, e, combo)

	    // we should ignore the next key up if the action is key down
	    // or keypress.  this is so if you finish a sequence and
	    // release the key the final key will not trigger a keyup
	    if (action !== 'keyup') {
	      characterFromEvent = __webpack_require__(131)
	      self.ignoreNextKeyup = characterFromEvent(e)
	    }

	    // weird race condition if a sequence ends with the key
	    // another sequence begins with
	    setTimeout(
	      function () {
	        self.resetSequences()
	      },
	      10
	    )
	  }

	  // loop through keys one at a time and bind the appropriate callback
	  // function.  for any key leading up to the final one it should
	  // increase the sequence. after the final, it should reset all sequences
	  //
	  // if an action is specified in the original bind call then that will
	  // be used throughout.  otherwise we will pass the action that the
	  // next key in the sequence should match.  this allows a sequence
	  // to mix and match keypress and keydown events depending on which
	  // ones are better suited to the key provided
	  for (var j = 0; j < keys.length; ++j) {
	    var isFinal = j + 1 === keys.length
	    var wrappedCallback = isFinal ? callbackAndReset : increaseSequence(action || self.getKeyInfo(keys[j + 1]).action)
	    self.bindSingle(keys[j], wrappedCallback, action, combo, j)
	  }
	}


/***/ },
/* 149 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'
	/**
	 * called to set a 1 second timeout on the specified sequence
	 *
	 * this is so after each key press in the sequence you have 1 second
	 * to press the next key before you have to start over
	 *
	 * @returns void
	 */
	module.exports = function () {
	  var self = this

	  clearTimeout(self.resetTimer)
	  self.resetTimer = setTimeout(
	    function () {
	      self.resetSequences()
	    },
	    1000
	  )
	}


/***/ },
/* 150 */
/***/ function(module, exports, __webpack_require__) {

	var off = __webpack_require__(129).off
	module.exports = function () {
	  var self = this
	  var element = self.element

	  off(element, 'keypress', self.eventHandler)
	  off(element, 'keydown', self.eventHandler)
	  off(element, 'keyup', self.eventHandler)
	}


/***/ },
/* 151 */
/***/ function(module, exports) {

	/* eslint-env node, browser */
	'use strict'

	module.exports = function () {
	  var self = this

	  self.instances.forEach(function (combokeys) {
	    combokeys.reset()
	  })
	}


/***/ },
/* 152 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var App = __webpack_require__(2);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var Drop = __webpack_require__(94);
	var App = __webpack_require__(2);
	var namespace = App.prototype.namespace('dropdown');

	var _Drop = Drop.createContext({
	  classPrefix: namespace
	});

	var defaults = {
	  position: 'bottom left',
	  openOn: 'click',
	  classes: namespace + '-theme-basic',
	  remove: true
	};

	var Dropdown = Behavior.extend({

	  _initialized: [],

	  initialize: function(options){
	    this.options = _.extend({}, defaults, options);

	    _.bindAll(this, 'onOpen', 'onClose', 'onSelect', 'onKeydown');

	    // define ui
	    this.ui = {
	      target: '*[data-toggle="' + namespace + '"]'
	    };
	  },

	  events: {
	    'click @ui.target': 'onClick'
	  },

	  onBeforeRender: function(){
	    // close open dropdowns on re-render
	    this.close();
	  },

	  onClick: function(e){
	    var target = e.currentTarget;

	    if(this._initialized.indexOf(target) !== -1) {
	      return;
	    }

	    var drop = this.initDrop(target);
	    this._initialized.push(target);

	    // first open
	    drop.open();
	  },

	  initDrop: function(target){

	    // default is .dropdown-list
	    var content = this.view.dropdownContent || this.view.$('.dropdown-list')[0];

	    // drop instance
	    var options = _.extend({}, this.options, {
	      target  : target,
	      content : content
	    });
	    this.drop = new _Drop(options);

	    // drop events
	    this.drop.on('open', this.onOpen);
	    this.drop.on('close', this.onClose);
	    $('li', this.drop.content).on('click', this.onSelect);
	    $(target).on('keydown', this.onKeydown);

	    return this.drop;

	  },

	  open: function(){
	    if(this.drop){
	      this.drop.open();
	    }
	  },

	  close: function(){
	    if(this.drop){
	      this.drop.close();
	    }
	  },

	  onOpen: function(){
	    this.view.triggerMethod('dropdown:open');
	  },

	  onClose: function(){
	    this.view.triggerMethod('dropdown:close');
	  },

	  onSelect: function(e){
	    this.view.triggerMethod('dropdown:select', e);
	    this.close();
	  },

	  onKeydown: function(e){

	    // esc
	    if(e.which === 27){
	      return this.close();
	    }

	    // pass keypress if open
	    if( this.drop.isOpened() ){
	      return this.view.triggerMethod('target:keydown', e);
	    }

	    this.open();

	  }

	});

	module.exports = Dropdown;
	App.prototype.set('Behaviors.Dropdown', Dropdown);

/***/ },
/* 153 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var Tooltip = __webpack_require__(93);
	var Radio = __webpack_require__(4);
	var App = __webpack_require__(2);
	var polyglot = __webpack_require__(10);

	var View = ItemView.extend({
	  template: 'modals.hotkeys',

	  initialize: function () {
	    this.hotkeys = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'hotkeys'
	    });

	    this.modal = {
	      header: {
	        title: polyglot.t('titles.hotkeys')
	      },
	      footer: {
	        show: false
	      }
	    };
	  },

	  behaviors: {
	    Tooltip: {
	      behaviorClass: Tooltip
	    }
	  },

	  templateHelpers: function(){
	    return this.hotkeys;
	  }

	});

	module.exports = View;
	App.prototype.set('HeaderApp.Views.HelpModal', View);

/***/ },
/* 154 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var App = __webpack_require__(2);
	var Radio = __webpack_require__(4);
	var hbs = __webpack_require__(7);
	var Tmpl = __webpack_require__(155);

	var View = ItemView.extend({
	  tagName: 'ul',
	  template: hbs.compile( Tmpl ),

	  initialize: function(){
	    _.bindAll(this, 'open', 'close');
	  },

	  templateHelpers: function(){
	    return Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'menu'
	    });
	  },

	  ui: {
	    menuItem: 'li a'
	  },

	  events: {
	    'click @ui.menuItem': 'close'
	  },

	  open: function(){
	    // open menu & append backdrop
	    $('body').addClass('menu-open');
	    this.backdrop = $('<div class="modal-backdrop in"></div>');
	    this.backdrop.height('100%');
	    $('body').append(this.backdrop);

	    // listen for clicks on backdrop
	    this.backdrop.on('click', this.close);
	  },

	  close: function(){
	    // close menu
	    $('body').removeClass('menu-open');

	    // teardown backdrop
	    if(this.backdrop) {
	      this.backdrop.remove();
	      delete this.backdrop;
	    }
	  }

	});

	module.exports = View;
	App.prototype.set('HeaderApp.Views.Menu', View);

/***/ },
/* 155 */
/***/ function(module, exports) {

	module.exports = "{{#each []}}\n<li class=\"{{id}}\">\n  <a href=\"{{href}}\">\n    <i class=\"icon-{{id}} icon-lg\"></i>\n    {{label}}\n  </a>\n</li>\n{{/each}}"

/***/ },
/* 156 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var Radio = __webpack_require__(4);
	var App = __webpack_require__(2);
	var $ = __webpack_require__(6);
	var polyglot = __webpack_require__(10);

	var View = ItemView.extend({
	  template: function(){
	    return '<i class="icon icon-spinner"></i>';
	  },

	  modal: {
	    footer: {
	      buttons: [
	        { action: 'close' }
	      ]
	    }
	  },

	  initialize: function(){
	    var self = this,
	      ajaxurl = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'ajaxurl'
	    });

	    $.get( ajaxurl, {
	      'action'  : 'wc_pos_get_modal',
	      'template': 'browser'
	    }).always(function(template){
	      self.update(template);
	    });

	    this.modal.header = {
	      title: polyglot.t('messages.browser')
	    };
	  },

	  update: function(template){
	    this.$el.html(template);
	  }
	});

	module.exports = View;
	App.prototype.set('HeaderApp.Views.BrowserModal', View);

/***/ },
/* 157 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var Drop = __webpack_require__(94);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var Mn = __webpack_require__(3);
	var App = __webpack_require__(2);
	var namespace = App.prototype.namespace('popover');

	var _Drop = Drop.createContext({
	  classPrefix: namespace
	});

	var defaults = {
	  position: 'bottom center',
	  openOn: undefined, // manual trigger
	  classes: namespace + '-theme-arrows',
	  constrainToWindow: true,
	  constrainToScrollParent: false
	};

	module.exports = Service.extend({
	  channelName: 'popover',

	  initialize: function(){

	    this.channel.reply({
	      'open' : this.open,
	      'close' : this.close
	    }, this);

	    _.bindAll(this, 'onClick');

	  },

	  open: function(options) {
	    options = _.extend({}, defaults, options);

	    // close any open popovers
	    this.close();

	    // new Drop instance
	    this.drop = new _Drop(options);

	    // attach region
	    this.drop.region = new Mn.Region({
	      el: this.drop.content
	    });

	    // listeners
	    $(document).on('click', this.onClick);
	    options.view.on('show', this.drop.open, this.drop);

	    // show
	    this.drop.region.show(options.view);

	    // return the drop instance
	    return this.drop;
	  },

	  close: function(){
	    if( this.drop ){
	      this.drop.region.empty();
	      this.drop.destroy();
	      this.drop = undefined;
	      $(document).off('click', this.onClick);
	    }
	  },

	  onClick: function(e){
	    if( e.target === this.drop.target ||
	      $(e.target).closest('.popover').length ){
	      return;
	    }

	    this.close();
	  }

	});

/***/ },
/* 158 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var hbs = __webpack_require__(7);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var debug = __webpack_require__(13)('print');
	var Radio = __webpack_require__(4);
	var App = __webpack_require__(2);

	module.exports = Service.extend({
	  channelName: 'print',

	  initialize: function(){
	    _.bindAll(this, 'mediaQueryListener', 'beforePrint', 'afterPrint');
	    this.start();
	  },

	  onStart: function(){
	    this.channel.reply({
	      'print' : this.print
	    }, this);
	  },

	  onStop: function(){
	    this.channel.reset();
	  },

	  /* jshint -W071 */
	  print: function(options){
	    var template = this.template(options),
	        iframe = this.init();

	    this.deferred = $.Deferred();

	    // insert template
	    iframe.document.write(template);

	    // print once loaded
	    var loaded = function(){
	      iframe.focus(); // required for IE
	      iframe.print();
	    };

	    // get the first image, ie: logo
	    var logo = iframe.document.getElementsByTagName('img')[0];

	    if( logo ){
	      logo.onload = loaded;
	    } else {
	      loaded();
	    }

	    return this.deferred;
	  },
	  /* jshint +W071 */

	  /**
	   * creates an iframe and stores reference
	   * returns a reference to the iframe window
	   */
	  init: function(){
	    if(this.iframe){
	      this.iframe.remove();
	    }

	    this.iframe = $('<iframe>')
	      .attr('name', 'iframe')
	      .css({visibility:'hidden',position:'-fixed',right:'0',bottom:'0'})
	      .appendTo('body');

	    // print events for Chrome 9+ & Safari 5.1+
	    if (frames['iframe'].matchMedia) {
	      var mediaQueryList = frames['iframe'].matchMedia('print');
	      mediaQueryList.addListener(this.mediaQueryListener);
	    }

	    return frames['iframe'];
	  },

	  mediaQueryListener: function(mql){
	    if (mql.matches) {
	      this.beforePrint();
	    } else {
	      // delay fixes weird behavior
	      // mediaQueryList seems to trigger both events on init
	      _.delay(this.afterPrint);
	    }
	  },

	  beforePrint: function(){
	    debug('printing ...');
	  },

	  afterPrint: function(){
	    this.iframe.remove();
	    this.iframe = undefined;
	    debug('printing finished');
	    this.deferred.resolve();
	  },

	  /* jshint -W074 */
	  /* todo: refactor print service with view */
	  template: function(options){
	    options = options || {};

	    if(!options.model){
	      return;
	    }

	    var tax = Radio.request('entities', 'get', {
	        type: 'option',
	        name: 'tax'
	      }) || {};
	    var ReceiptView = App.prototype.ReceiptView.prototype;
	    var data = ReceiptView.prepare( options.model.toJSON(), tax );
	    var template = _.get(hbs.Templates, ['print', 'receipt'], '');
	    return hbs.compile( template )( data );
	  }
	  /* jshint +W074 */

	});

/***/ },
/* 159 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(20);
	var View = __webpack_require__(160);

	module.exports = Service.extend({

	  channelName: 'numpad',

	  initialize: function(){
	    this.channel.reply({
	      'view' : this.view
	    }, this);
	  },

	  view: function(options){
	    return new View(options);
	  }

	});

/***/ },
/* 160 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(88);
	var App = __webpack_require__(2);
	var hbs = __webpack_require__(7);
	var Tmpl = __webpack_require__(161);
	var Model = __webpack_require__(162);
	var _ = __webpack_require__(5);
	var $ = __webpack_require__(6);
	var accounting = __webpack_require__(9);
	var Radio = __webpack_require__(4);
	var AutoGrow = __webpack_require__(163);
	var cashKeys = __webpack_require__(164);
	var Utils = __webpack_require__(8);

	// numpad header input btns
	// - could be improved if _.result allowed custom bind?
	var inputBtns = {
	  amount: function(){
	    return {
	      left: { addOn: this.symbol }
	    };
	  },
	  discount: function(){
	    return {
	      left: { btn: this.symbol },
	      right: { btn: '%' },
	      toggle: true
	    };
	  },
	  cash: function(){
	    return {
	      left: { addOn: this.symbol }
	    };
	  },
	  quantity: function(){
	    return {
	      left: { btn: '<i class="icon icon-minus"></i>' },
	      right: { btn: '<i class="icon icon-plus"></i>' }
	    };
	  }
	};

	// numpad extra keys
	// - could be improved if _.result allowed custom bind?
	var extraKeys = {
	  amount: function(){},
	  discount: function(){
	    var discountKeys = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'discount_keys'
	    }) || {};
	    return _.map(discountKeys, function(n){ return n + '%'; });
	  },
	  cash: function(value){
	    var denominations = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'denominations'
	    }) || {};
	    return _.map(cashKeys(value, denominations), function(n){
	      if(this.value === 0){ return n; }
	      return Utils.formatNumber(n);
	    }, this);
	  },
	  quantity: function(){}
	};


	var View = FormView.extend({
	  template: hbs.compile(Tmpl),

	  viewOptions: [
	    'numpad', 'label', 'value', 'decimal', 'symbol'
	  ],

	  initialize: function(options){
	    options = options || {};

	    options = _.defaults(options, {
	      label   : 'Numpad',
	      numpad  : 'amount',
	      value   : 0,
	      decimal : accounting.settings.currency.decimal,
	      symbol  : accounting.settings.currency.symbol
	    });

	    this.mergeOptions(options, this.viewOptions);

	    this.model = new Model({}, options);
	  },

	  behaviors: {
	    AutoGrow: {
	      behaviorClass: AutoGrow
	    }
	  },

	  bindings: {
	    'input[name="value"]': {
	      observe: ['value', 'percentage', 'active'],
	      onGet: function(arr){
	        var val = arr[2] === 'percentage' ? arr[1] : arr[0];
	        //var precision;
	        //if(arr[2] === 'percentage' || this.numpad === 'quantity'){
	        //  precision = 'auto';
	        //}
	        //return Utils.formatNumber(val, precision);
	        return val;
	      },
	      onSet: function(val, opts){
	        opts.view.model.setActive(val);
	      }
	    },
	    '.numpad-discount [data-btn="left"]': {
	      updateMethod: 'html',
	      observe: ['value', 'percentage', 'active'],
	      onGet: function(arr){
	        if(arr[2] === 'percentage'){
	          return Utils.formatMoney(arr[0]);
	        } else {
	          return this.symbol;
	        }
	      }
	    },
	    '.numpad-discount [data-btn="right"]': {
	      updateMethod: 'html',
	      observe: ['percentage', 'value', 'active'],
	      onGet: function(arr){
	        if(arr[2] === 'percentage'){
	          return '%';
	        } else {
	          return accounting.toFixed(arr[0], 0) + '%';
	        }
	      }
	    }
	  },

	  templateHelpers: function(){
	    var data = {
	      numpad  : this.numpad,
	      label   : this.label,
	      input   : inputBtns[this.numpad].call(this),
	      keys    : extraKeys[this.numpad].call(this, this.value),
	      decimal : this.decimal,
	      'return': 'return'
	    };

	    return data;
	  },

	  ui: {
	    input   : '.numpad-header input[name="value"]',
	    toggle  : '.numpad-header .input-group',
	    common  : '.numpad-body .common .btn',
	    discount: '.numpad-body .discount .btn',
	    cash    : '.numpad-body .cash .btn',
	    keys    : '.numpad-body .btn'
	  },

	  events: {
	    'click @ui.toggle a': 'toggle',
	    'click @ui.common'  : 'commonKeys',
	    'click @ui.discount': 'discountKeys',
	    'click @ui.cash'    : 'cashKeys',
	    'mousedown @ui.keys': 'keyPress',
	    'keyup @ui.input'   : 'enter'
	  },

	  onRender: function(){
	    if(window.Modernizr.touch) {
	      this.ui.input.attr('readonly', true);
	    }
	  },

	  toggle: function(e){
	    e.preventDefault();
	    var modifier = $(e.currentTarget).data('btn');

	    if(this.numpad === 'quantity'){
	      this.model.quantity( modifier === 'right' ? 'increase' : 'decrease' );
	    }

	    if(this.numpad === 'discount'){
	      this.ui.toggle.toggleClass('toggle');
	      this.model.toggle('percentage');
	    }
	  },

	  /* jshint -W074 */
	  commonKeys: function(e){
	    e.preventDefault();
	    var key = $(e.currentTarget).data('key');

	    switch(key) {
	      case 'ret':
	        this.trigger('input', this.model.getFloatValue(), this.model);
	        return;
	      case 'del':
	        if(this._hasSelection) {
	          this.model.clearInput();
	        }
	        this.model.backSpace();
	        break;
	      case '+/-':
	        this.model.plusMinus();
	        break;
	      case '.':
	        this.model.decimal();
	        break;
	      default:
	        if(this._hasSelection) {
	          this.model.clearInput();
	        }
	        this.model.key(key);
	    }

	  },
	  /* jshint +W074 */

	  discountKeys: function(e){
	    e.preventDefault();
	    var key = $(e.currentTarget).data('key');
	    this.model.set('active', 'percentage');
	    this.model.set('percentage', key.replace('%', ''));
	    this.ui.toggle.addClass('toggle');
	  },

	  cashKeys: function(e){
	    e.preventDefault();
	    var key = $(e.currentTarget).data('key');
	    key = Utils.unformat(key);
	    this.model.clearInput().key(key);
	  },

	  // on keypress, check if input selected
	  keyPress: function(){
	    var sel = window.getSelection();
	    this._hasSelection = sel.toString() === this.ui.input.val();
	  },

	  enter: function(e){
	    if(e.which === 13){
	      this.trigger('input', this.model.getFloatValue(), this.model);
	    }
	  }

	});

	module.exports = View;
	App.prototype.set('Components.Numpad.View', View);

/***/ },
/* 161 */
/***/ function(module, exports) {

	module.exports = "<div class=\"numpad numpad-{{numpad}}\">\n  <div class=\"popover-header numpad-header\">\n    <strong class=\"title\">{{label}}</strong>\n\n    {{#if input}}\n      <div class=\"input-group {{#if input.toggle}}btn-group-toggle{{/if}}\">\n        {{#if input.left}}\n          {{#if input.left.addOn}}<span class=\"input-group-addon\">{{{input.left.addOn}}}</span>{{/if}}\n          {{#if input.left.btn}}<span class=\"input-group-btn\"><a href=\"#\" data-btn=\"left\" class=\"btn btn-secondary\">{{{input.left.btn}}}</a></span>{{/if}}\n        {{/if}}\n        <input type=\"text\" name=\"value\" class=\"form-control autogrow\">\n        {{#if input.right}}\n          {{#if input.right.btn}}<span class=\"input-group-btn\"><a href=\"#\" data-btn=\"right\" class=\"btn btn-secondary\">{{{input.right.btn}}}</a></span>{{/if}}\n          {{#if input.right.addOn}}<span class=\"input-group-addon\">{{{input.right.addOn}}}</span>{{/if}}\n        {{/if}}\n      </div>\n    {{/if}}\n\n  </div>\n\n  <div class=\"popover-body numpad-body\">\n    <div class=\"keys common three-cols\">\n      <div class=\"row\">\n        <button data-key=\"1\" class=\"btn\">1</button>\n        <button data-key=\"2\" class=\"btn\">2</button>\n        <button data-key=\"3\" class=\"btn\">3</button>\n      </div>\n      <div class=\"row\">\n        <button data-key=\"4\" class=\"btn\">4</button>\n        <button data-key=\"5\" class=\"btn\">5</button>\n        <button data-key=\"6\" class=\"btn\">6</button>\n      </div>\n      <div class=\"row\">\n        <button data-key=\"7\" class=\"btn\">7</button>\n        <button data-key=\"8\" class=\"btn\">8</button>\n        <button data-key=\"9\" class=\"btn\">9</button>\n      </div>\n      <div class=\"row\">\n        <button data-key=\"0\" class=\"btn\">0</button>\n        <button data-key=\"00\" class=\"btn\">00</button>\n        <button data-key=\".\" class=\"btn decimal\">{{{decimal}}}</button>\n      </div>\n    </div>\n    <div class=\"keys common extra-keys\">\n      <button data-key=\"del\" class=\"btn\"><i class=\"icon-delete\"><span>del</span></i></button>\n      <button data-key=\"+/-\" class=\"btn\">+/-</button>\n      <button data-key=\"ret\" class=\"btn two-rows return\"><small>{{return}}</small></button>\n    </div>\n\n    {{#if keys}}\n      <div class=\"keys extra-keys {{numpad}}\">\n        {{#each keys}}\n          <button data-key=\"{{this}}\" class=\"btn\">{{this}}</button>\n        {{/each}}\n      </div>\n    {{/if}}\n\n  </div>\n</div>"

/***/ },
/* 162 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(16);
	var _ = __webpack_require__(5);

	module.exports = bb.Model.extend({

	  dec: '',

	  defaults: {
	    active: 'value'
	  },

	  precision: 4,

	  initialize: function(attributes, options){
	    options = options || {};
	    this.numpad = options.numpad;

	    this.set({ value: options.value.toString() });

	    if(options.precision){
	      this.precision = options.precision;
	    }

	    if(options.original){
	      this.initPercentage(options);
	    }
	  },

	  initPercentage: function(options){
	    if(options.percentage === 'off'){
	      this.percentageOff = true;
	    }

	    this.set({ original: options.original.toString() });
	    this.set({ percentage: this.calcPercentage() });

	    this.on({
	      'change:value': function(){
	        this.set({ percentage: this.calcPercentage() }, { silent: true });
	      },
	      'change:percentage': function(){
	        this.set({ value: this.calcValue() }, { silent: true });
	      }
	    });
	  },

	  getFloatValue: function(){
	    return parseFloat( this.get('value') );
	  },

	  getActive: function(type){
	    var active = this.get( this.get('active') );
	    return type === 'float' ? parseFloat(active) : active.toString();
	  },

	  setActive: function(num){
	    if(this.dec === '.'){
	      this.dec = '';
	    }
	    if( _.isNaN(num) ){
	      num = 0;
	    }
	    this.set( this.get('active'), this.round(num) );
	  },

	  backSpace: function(){
	    var num = this.getActive().slice(0, -1);
	    // remove trailing decimal
	    if( num.slice(-1) === '.' ){
	      num = num.slice(0, -1);
	    }
	    this.setActive( num );
	    return this;
	  },

	  plusMinus: function(){
	    var num = this.getActive('float') * -1;
	    this.setActive( num );
	    return this;
	  },

	  clearInput: function(){
	    this.setActive( '' );
	    return this;
	  },

	  key: function(key){
	    var num = this.getActive();
	    if(this.decimalPlaces(num) < this.precision){
	      this.setActive( num + this.dec + key );
	    }
	    return this;
	  },

	  decimal: function(){
	    this.dec = this.getActive().indexOf('.') === -1 ? '.' : '';
	    return this;
	  },

	  quantity: function( type ) {
	    var num = this.getActive('float');
	    this.setActive( (type === 'increase' ? ++num : --num) );
	    return this;
	  },

	  calcPercentage: function(){
	    var value      = parseFloat( this.get('value') ),
	      original   = parseFloat( this.get('original') ),
	      percentage = ( value / original ) * 100;

	    if(this.percentageOff){
	      return this.round(100 - percentage);
	    }

	    return this.round(percentage);
	  },

	  calcValue: function(){
	    var percentage = parseFloat( this.get('percentage') ),
	      original   = parseFloat( this.get('original') ),
	      multiplier = percentage / 100;

	    if(this.percentageOff){
	      return this.round(( 1 - multiplier ) * original);
	    }

	    return this.round(multiplier * original);
	  },

	  toggle: function(attr){
	    this.set({ active: (attr === this.get('active') ? 'value' : attr) });
	  },

	  round: function(val){
	    val = val.toString();
	    if(this.decimalPlaces(val) > this.precision){
	      val = parseFloat(val).toFixed(this.precision);
	      val = parseFloat(val).toString();
	    }
	    return val;
	  },

	  decimalPlaces: function(val){
	    return val.replace(/^-?\d*\.?|0+$/g, '').length;
	  }

	});

/***/ },
/* 163 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var App = __webpack_require__(2);

	var AutoGrow = Behavior.extend({

	  initialize: function(options){
	    this.options = options || {};
	    _.defaults(this.options, {
	      padding: 20
	    });

	    this.tester = $('#autogrow-tester');
	    if( this.tester.length === 0 ) {
	      this.tester = $('<div id="autogrow-tester" />')
	        .css({
	          opacity     : 0,
	          top         : -9999,
	          left        : -9999,
	          position    : 'absolute',
	          whiteSpace  : 'nowrap'
	        });
	      $('body').append(this.tester);
	    }

	  },

	  ui: {
	    target: '.autogrow'
	  },

	  events: {
	    'input @ui.target' : 'autoGrow'
	  },

	  onShow: function() {
	    _.each( this.ui.target, function( target ) {
	      this.autoGrow( $(target) );
	    }, this);
	  },

	  autoGrow: function( e ) {
	    var target  = e.target ? $(e.target) : e ;
	    var value   = target.is('input') ? target.val() : target.text();

	    value = value.replace(/&/g, '&amp;')
	      .replace(/\s/g,'&nbsp;')
	      .replace(/</g, '&lt;')
	      .replace(/>/g, '&gt;');

	    this.tester.html(value);
	    var width = this.tester.width() + this.options.padding;
	    target.css({ width: width });
	  }

	});

	module.exports = AutoGrow;
	App.prototype.set('Behaviors.AutoGrow', AutoGrow);

/***/ },
/* 164 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(5);
	var accounting = __webpack_require__(9);

	/* jshint -W071 */

	function round(num){
	  return parseFloat( accounting.toFixed(num, 2) );
	}

	function nearest(num, dem){
	  var n = Math.ceil(num / dem) * dem;
	  return round(n);
	}

	module.exports = function(amount, denominations){
	  var keys = [amount];

	  if(amount === 0) {
	    return denominations.notes.slice(-4);
	  }

	  // remove 1 & 2 cents
	  _.pull( denominations.coins, 0.01, 0.02 );

	  // find nearest match for coins
	  _.each( denominations.coins, function(coin) {
	    keys.push( nearest(amount, coin) );
	  });

	  // unique, sorted
	  keys = _.chain(keys).uniq().sortBy().value();

	  // higher rep of coin for low amounts
	  if(amount > denominations.notes[0]){
	    keys.slice(0, 3);
	  }

	  // find nearest match for notes
	  _.each( denominations.notes, function(note) {
	    keys.push( nearest(amount, note) );
	  });

	  // return 4 results - unique, sorted
	  return _.chain(keys).uniq().sortBy().value().slice(1, 5);
	};
	/* jshint +W071 */

/***/ },
/* 165 */
/***/ function(module, exports, __webpack_require__) {

	var App = __webpack_require__(2);
	var Router = __webpack_require__(81);
	var LayoutView = __webpack_require__(166);
	var Products = __webpack_require__(167);
	var CartRoute = __webpack_require__(180);
	var CheckoutRoute = __webpack_require__(192);
	var ReceiptRoute = __webpack_require__(200);
	var Radio = __webpack_require__(4);
	var bb = __webpack_require__(16);

	var POSRouter = Router.extend({
	  routes: {
	    '(cart)(/:id)(/)'  : 'showCart',
	    'checkout(/:id)(/)': 'showCheckout',
	    'receipt(/:id)(/)' : 'showReceipt'
	  },

	  initialize: function(options) {
	    this.container = options.container;
	    this.channel.reply({
	      'show:cart'     : this.showCart,
	      'show:checkout' : this.showCheckout,
	      'show:receipt'  : this.showReceipt,
	      'add:to:cart'   : this.addToCart
	    }, this);
	  },

	  onBeforeEnter: function() {
	    this.layout = new LayoutView();
	    this.container.show(this.layout);
	    this.initOrders();
	  },

	  initOrders: function(){
	    if(this.orders){ return; }

	    // attach orders
	    this.orders = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: 'orders'
	    });

	    // listen to order collection
	    this.listenTo(this.orders, {
	      'add'    : this.addOrder,
	      'remove' : this.removeOrder
	    });
	  },

	  onBeforeRoute: function(){
	    //this.setActiveTab();
	    this.showProducts();
	    Radio.request('header', 'update:title', '');
	  },

	  setActiveTab: function(){
	    var tab = bb.history.getFragment() === '' ? 'left' : 'right';
	    this.container.tabs.setActive(tab);
	  },

	  showProducts: function(){
	    if( this.layout.getRegion('left').hasView() ){
	      return;
	    }
	    var products = new Products({
	      container : this.layout.getRegion('left')
	    });
	    products.enter();
	  },

	  showCart: function() {
	    return new CartRoute({
	      container : this.layout.getRegion('right'),
	      collection: this.orders
	    });
	  },

	  showCheckout: function() {
	    return new CheckoutRoute({
	      container : this.layout.getRegion('right'),
	      collection: this.orders
	    });
	  },

	  showReceipt: function() {
	    var autoPrint = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'auto_print'
	    });

	    return new ReceiptRoute({
	      container : this.layout.getRegion('right'),
	      collection: this.orders,
	      autoPrint : autoPrint
	    });
	  },

	  /**
	   * Add to cart only when cart route is active
	   */
	  addToCart: function(options){
	    if(this._currentRoute instanceof CartRoute){
	      this.orders.addToCart(options);
	    }
	  },

	  addOrder: function(order){
	    if(this._currentRoute instanceof CartRoute){
	      //bb.history.navigate('cart/' + order.id);
	      if(order.isEditable()){
	        this.execute(this.showCart, [order.id]);
	      }
	    }
	  },

	  removeOrder: function(){
	    if(this._currentRoute instanceof CartRoute){
	      bb.history.navigate('');
	      this.execute(this.showCart);
	    }
	  }

	});

	module.exports = POSRouter;
	App.prototype.set('POSApp.Router', POSRouter);

/***/ },
/* 166 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);

	module.exports = LayoutView.extend({
	  columns: 2,

	  template: function(){
	    return '' +
	      '<section id="left"></section>' +
	      '<section id="right"></section>';
	  },

	  regions: {
	    left: '#left',
	    right: '#right'
	  }
	});

/***/ },
/* 167 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	var Layout = __webpack_require__(168);
	var Actions = __webpack_require__(169);
	var List = __webpack_require__(171);
	var Pagination = __webpack_require__(179);
	var Radio = __webpack_require__(4);
	var _ = __webpack_require__(5);
	var polyglot = __webpack_require__(10);

	module.exports = Route.extend({

	  initialize: function (options) {
	    this.container  = options.container;
	    this.filtered   = Radio.request('entities', 'get', {
	      type    : 'filtered',
	      name    : 'products',
	      perPage : 10
	    });
	    this.setTabLabel({
	      tab   : 'left',
	      label : polyglot.t('titles.products')
	    });
	  },

	  fetch: function() {
	    var collection = this.filtered.superset();
	    if( collection.isNew() ){
	      return collection.fetch()
	        .then(function(){
	          collection.fullSync();
	        });
	    } else {
	      this.filtered
	        .resetFilters()
	        .removeSort()
	        .setPage(0);
	    }
	  },

	  render: function() {
	    this.layout = new Layout();

	    this.listenTo(this.layout, 'show', function () {
	      this.showActions();
	      this.showTabs();
	      this.showProducts();
	      this.showPagination();
	    });

	    this.container.show( this.layout );
	  },

	  showActions: function() {
	    var view = new Actions({
	      collection: this.filtered
	    });

	    this.layout.getRegion('actions').show(view);
	  },

	  showTabs: function() {
	    var view = Radio.request('tabs', 'view', {
	      tabs: this.tabsArray()
	    });

	    this.listenTo(view.collection, 'active:tab', function(model) {
	      this.filtered.query('tab', model.id);
	    });

	    // show tabs component
	    this.layout.getRegion('tabs').show(view);
	    this.tabs = view;
	  },

	  showProducts: function() {
	    var view = new List({
	      collection: this.filtered
	    });
	    this.layout.getRegion('list').show(view);
	  },

	  tabsArray: function(){
	    var tabs = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tabs'
	    });
	    return _.map(tabs);
	  },

	  showPagination: function(){
	    var view = new Pagination({
	      collection: this.filtered
	    });
	    this.layout.getRegion('footer').show(view);
	  }

	});

/***/ },
/* 168 */
/***/ function(module, exports, __webpack_require__) {

	var hbs = __webpack_require__(7);
	var LayoutView = __webpack_require__(18);
	var App = __webpack_require__(2);

	var Layout = LayoutView.extend({

	  template: hbs.compile('' +
	    '<div class="panel-header list-actions products-header"></div>' +
	    '<div class="products-tabs"></div>' +
	    '<div class="panel-body list products-list list-striped"></div>' +
	    '<div class="panel-footer products-footer"></div>'
	  ),

	  regions: {
	    actions   : '.products-header',
	    tabs      : '.products-tabs',
	    list      : '.products-list',
	    footer    : '.products-footer'
	  },

	  attributes: {
	    'class'   : 'panel products'
	  }

	});

	module.exports = Layout;
	App.prototype.set('POSApp.Products.Layout', Layout);

/***/ },
/* 169 */
/***/ function(module, exports, __webpack_require__) {

	var View = __webpack_require__(61);
	var App = __webpack_require__(2);
	var Filter = __webpack_require__(170);
	var HotKeys = __webpack_require__(118);
	var Radio = __webpack_require__(4);
	var Dropdown = __webpack_require__(152);
	var $ = __webpack_require__(6);

	var Actions = View.extend({
	  template: 'pos.products.filter',

	  initialize: function(){
	    var products = this.collection;
	    var self = this;

	    /**
	     * match:barcode is triggered before filter is complete
	     * this affects the filtered collection pagination
	     * todo: refactor and fix this
	     */
	    this.listenTo(products.superset(), 'match:barcode', function(model){
	      products.once('paginated:change:page', function(){
	        self.triggerMethod('clear');
	        self.ui.searchField.blur();
	      });
	      Radio.request('router', 'add:to:cart', model);
	    });
	  },

	  behaviors: {
	    Filter: {
	      behaviorClass: Filter
	    },
	    HotKeys: {
	      behaviorClass: HotKeys
	    },
	    Dropdown: {
	      behaviorClass: Dropdown
	    }
	  },

	  keyEvents: {
	    'search'  : 'barcodeModeOff',
	    'barcode' : 'barcodeModeOn'
	  },

	  ui: {
	    modeIcon    : '*[data-toggle="dropdown"] i',
	    searchBtn   : '*[data-action="search"]',
	    barcodeBtn  : '*[data-action="barcode"]',
	    searchField : 'input[type=search]'
	  },

	  onRender: function(){
	    this.barcodeModeOff();
	  },

	  onDropdownSelect: function(e){
	    if( $(e.currentTarget).data('action') === 'barcode' ){
	      return this.barcodeModeOn();
	    }
	    this.barcodeModeOff();
	  },

	  toggleBarcodeMode: function(){
	    if(this._mode === 'barcode'){
	      return this.barcodeModeOff();
	    }
	    this.barcodeModeOn();
	  },

	  barcodeModeOn: function(e){
	    if(e) { e.preventDefault(); }
	    this._mode = 'barcode';
	    this.ui.modeIcon
	      .removeClass('icon-search')
	      .addClass('icon-barcode');
	    this.ui.searchField
	      .attr('placeholder', this.ui.barcodeBtn.text())
	      .focus()
	      .trigger('keyup');
	  },

	  barcodeModeOff: function(e){
	    if(e) { e.preventDefault(); }
	    this._mode = undefined;
	    this.ui.modeIcon
	      .removeClass('icon-barcode')
	      .addClass('icon-search');
	    this.ui.searchField
	      .attr('placeholder', this.ui.searchBtn.text())
	      .focus()
	      .trigger('keyup');
	  }
	});

	module.exports = Actions;
	App.prototype.set('POSApp.Products.Actions', Actions);

/***/ },
/* 170 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var _ = __webpack_require__(5);
	var App = __webpack_require__(2);
	var Combokeys = __webpack_require__(119);
	var Radio = __webpack_require__(4);

	var Filter = Behavior.extend({

	  initialize: function(){

	    this.listenTo(this.view.options.collection.superset(), {
	      'start:fullSync': this.syncStart,
	      'end:fullSync': this.syncEnd
	    });

	    this.hotkeys = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'hotkeys'
	    }) || {};

	    this.combokeys = new Combokeys(document.documentElement);
	    this.combokeys.bind(this.hotkeys.sync.key, _.bind( this.sync, this ));

	  },

	  ui: {
	    searchField : 'input[type=search]',
	    clearBtn    : '.clear',
	    clear       : '*[data-action="clear"]',
	    sync        : '*[data-action="sync"]'
	  },

	  events: {
	    'keyup @ui.searchField' : 'query',
	    'click @ui.clear'       : 'clear',
	    'click @ui.sync'        : 'sync'
	  },

	  /**
	   *
	   */
	  query: function(){
	    var value = this.ui.searchField.val();
	    if( value === '' ){
	      return this.clear();
	    }

	    // special case, filter mode, eg: barcode
	    if(this.view._mode){
	      value = [{
	        type  : 'prefix',
	        prefix: this.view._mode,
	        query : value
	      }];
	    }

	    this.ui.clearBtn.show();
	    this._query(value);
	  },

	  /**
	   *
	   */
	  _query: _.debounce( function(value){
	    this.view.collection
	      .query(value)
	      .firstPage();
	  }, 149),

	  /**
	   *
	   */
	  clear: function(e) {
	    if(e) { e.preventDefault(); }
	    this.view.collection.removeFilter('search');
	    this.ui.searchField.val('');
	    this.ui.clearBtn.hide();
	  },

	  /**
	   *
	   */
	  onRender: function(){
	    if(this.view.collection.hasFilter('search')){
	      var queryStr = this.view.collection._filtered._query;
	      if(queryStr){
	        this.ui.searchField.val(queryStr).trigger('keyup');
	      }
	    }
	  },

	  sync: function(e){
	    if(e) { e.preventDefault(); }
	    this.view.collection.superset().fullSync();
	  },

	  syncStart: function(){
	    this.ui.sync.children('i').addClass('icon-spin');
	  },

	  syncEnd: function(){
	    this.ui.sync.children('i').removeClass('icon-spin');
	  },

	  onDestroy: function(){
	    this.combokeys.unbind(this.hotkeys.sync.key);
	  },

	  onClear: function(){
	    this.clear();
	  }

	});

	module.exports = Filter;
	App.prototype.set('Behaviors.Filter', Filter);

/***/ },
/* 171 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var InfiniteListView = __webpack_require__(172);
	var Item = __webpack_require__(173);
	var App = __webpack_require__(2);
	var polyglot = __webpack_require__(10);

	var Empty = ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return polyglot.t('messages.no-products');
	  }
	});

	var List = InfiniteListView.extend({
	  childView: Item,
	  emptyView: Empty,
	  childViewContainer: 'ul'
	});

	module.exports = List;
	App.prototype.set('POSApp.Products.List', List);

/***/ },
/* 172 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(3);
	var app = __webpack_require__(2);
	var _ = __webpack_require__(5);
	//var $ = require('jquery');

	module.exports = app.prototype.InfiniteListView = Mn.CompositeView.extend({
	  className: 'list-infinite',
	  template: function(){
	    return '<div></div>' +
	      '<ul></ul>' +
	      '<div><i class="icon icon-spinner"></i>';
	  },

	  constructor: function() {
	    Mn.CompositeView.apply(this, arguments);

	    this.listenTo(this.collection.superset(), {
	      'start:processQueue': this.startLoading,
	      'end:processQueue': this.endLoading,
	      'end:fullSync': this.checkOverflow
	    });

	    this.on({
	      'all': _.debounce( this.checkOverflow, 250 )
	    });

	    _.bindAll(this, 'onScroll', 'loadMore', 'getOverflow');
	  },

	  // Marionette's default implementation ignores the index, always
	  // appending the new view to the end. Let's be a little more clever.
	  //appendHtml: function(collectionView, itemView, index){
	  //  if (!index) {
	  //    collectionView.$el.prepend(itemView.el);
	  //  } else {
	  //    $(collectionView.$('li')[index - 1]).after(itemView.el);
	  //  }
	  //},

	  onShow: function(){
	    this.container = this.$el.parent()[0];
	    this.$el.parent().scroll( _.throttle( this.onScroll, 250 ) );
	  },

	  onScroll: function(){
	    if(this.getOverflow() < 100){
	      this.loadMore();
	    }
	  },

	  checkOverflow: function(){
	    if(this.getOverflow() < 100){
	      this.loadMore();
	    }
	  },

	  /**
	   * returns overflow at bottom in px
	   * - added clientHeight check to prevent false trigger when div not drawn
	   * @returns {number}
	   */
	  getOverflow: function(){
	    var sH = this.container.scrollHeight,
	        cH = this.container.clientHeight,
	        sT = this.container.scrollTop;
	    return sH - cH - sT;
	  },

	  loadMore: function() {
	    // get next page from filtered collection
	    if (this.collection.hasNextPage()) {
	      return this.collection.appendNextPage();
	    }

	    // load more from queue
	    if (this.collection.superset().queue.length > 0) {
	      this.remoteFetchMore();
	    }
	  },

	  remoteFetchMore: function(){
	    this.collection.superset().processQueue({
	      filter: this.collection.getRemoteFilter()
	    });
	  },

	  startLoading: function(){
	    this.toggleLoading(true);
	  },

	  endLoading: function(){
	    this.toggleLoading(false);
	  },

	  toggleLoading: function(loading){
	    this.$el.toggleClass('loading', loading);
	  }

	});

/***/ },
/* 173 */
/***/ function(module, exports, __webpack_require__) {

	var App = __webpack_require__(2);
	var LayoutView = __webpack_require__(18);
	var Product = __webpack_require__(174);
	var Variations = __webpack_require__(177);

	/**
	 * @todo Abstract ListItemView
	 */

	var Layout = LayoutView.extend({

	  tagName: 'li',

	  className: function () {
	    return this.model.get('type');
	  },

	  template: function () {
	    return '' +
	      '<div class="list-item"></div>' +
	      '<div class="list-drawer"></div>';
	  },

	  regions: {
	    item    : '.list-item',
	    drawer  : '.list-drawer'
	  },

	  onRender: function(){
	    var view = new Product({
	      model: this.model
	    });

	    this.listenTo( view, {
	      'open:drawer'     : this.openDrawer,
	      'close:drawer'    : this.closeDrawer,
	      'toggle:drawer'   : this.toggleDrawer
	    });

	    this.getRegion('item').show(view);
	  },

	  openDrawer: function(options){
	    options = options || {};
	    options.model = this.model;
	    options.className = 'variations';
	    var view = new Variations(options);
	    this.getRegion('drawer').show(view);
	    this.$el.addClass('drawer-open');
	  },

	  closeDrawer: function(){
	    var drawer = this.getRegion('drawer');

	    drawer.$el.slideUp( 250, function(){
	      drawer.empty();
	      drawer.$el.show();
	    });

	    this.$el.removeClass('drawer-open');
	  },

	  toggleDrawer: function(options){
	    var drawer = this.getRegion('drawer'),
	        open = drawer.hasView();

	    if(open && options.filter){
	      return drawer.currentView.filterVariations(options.filter);
	    }

	    if(open){
	      this.closeDrawer();
	    } else {
	      this.openDrawer(options);
	    }
	  }

	});

	module.exports = Layout;
	App.prototype.set('POSApp.Products.Item.Layout', Layout);

/***/ },
/* 174 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var Tooltip = __webpack_require__(93);
	var Radio = __webpack_require__(4);
	var Variations = __webpack_require__(175);

	var Item = ItemView.extend({
	  template: 'pos.products.product',

	  className: 'list-row',

	  ui: {
	    add      : 'a[data-action="add"]',
	    vpopover : 'a[data-action="variations"]',
	    vdrawer  : '.title dl.variations dd a'
	  },

	  events: {
	    'click @ui.add' : 'addToCart',
	    'click @ui.vpopover' : 'variationsPopover',
	    'click @ui.vdrawer'  : 'variationsDrawer'
	  },

	  modelEvents: {
	    'change:updated_at': 'render'
	  },

	  behaviors: {
	    Tooltip: {
	      behaviorClass: Tooltip,
	      html: true
	    }
	  },

	  addToCart: function(e){
	    e.preventDefault();
	    Radio.request('router', 'add:to:cart', {model: this.model});
	  },

	  variationsPopover: function(e){
	    e.preventDefault();

	    var view = new Variations({
	      model: this.model
	    });

	    this.listenTo(view, 'add:to:cart', function(args){
	      var product = args.collection.models[0].toJSON();
	      Radio.request('router', 'add:to:cart', product);
	      Radio.request('popover', 'close');
	    });

	    var options = _.extend({
	      view      : view,
	      target    : e.target,
	      position  : 'right middle'
	    }, view.popover);

	    Radio.request('popover', 'open', options);
	  },

	  variationsDrawer: function(e){
	    e.preventDefault();
	    var options = {};

	    var name = $(e.target).data('name');
	    if(name){
	      options.filter = {
	        name: name,
	        option: $(e.target).text()
	      };
	    }

	    this.trigger('toggle:drawer', options);
	  },

	  templateHelpers: function(){
	    var variations = this.model.getVariations();
	    var data = variations ? {
	      price: variations.superset().range('price'),
	      sale_price: variations.superset().range('sale_price'),
	      regular_price: variations.superset().range('regular_price'),
	      product_variations: this.model.getVariationOptions()
	    } : {} ;
	    data.product_attributes = this.model.productAttributes();
	    return data;
	  }

	});

	module.exports = Item;
	App.prototype.set('POSApp.Products.Item', Item);

/***/ },
/* 175 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var Tmpl = __webpack_require__(176);
	var App = __webpack_require__(2);
	var hbs = __webpack_require__(7);
	var _ = __webpack_require__(5);
	var $ = __webpack_require__(6);
	var polyglot = __webpack_require__(10);

	// rough calculation of variation option size
	var hasManyOptions = function(variation){
	  var opts = variation.options.length,
	    chars = variation.options.reduce(function(total, opt){
	      return total + opt.length;
	    }, 0);
	  return (opts * 26) + (chars * 5) > 220;
	};

	var Variations = ItemView.extend({
	  template: hbs.compile(Tmpl),

	  popover: {
	    classes: 'popover-theme-arrows popover-variations'
	  },

	  initialize: function(){
	    this.collection = this.model.getVariations();
	    this.collection.resetFilters();
	  },

	  templateHelpers: function(){
	    var data = {};
	    data.variations = _.chain( this.model.getVariationOptions() )
	      .each(function(variation){
	        if(hasManyOptions(variation)){
	          variation.select = true;
	          variation.emptyOption = polyglot.t('messages.choose');
	        }
	      })
	      .value();
	    return data;
	  },

	  ui: {
	    add: '*[data-action="add"]',
	    btn: '.btn-group .btn',
	    select: 'select'
	  },

	  triggers: {
	    'click @ui.add': 'add:to:cart'
	  },

	  events: {
	    'click @ui.btn'     : 'onVariationSelect',
	    'change @ui.select' : 'onVariationSelect'
	  },

	  onVariationSelect: function(e){
	    var target = $(e.currentTarget);

	    // toggle
	    if( target.is('button') ){
	      target
	        .addClass('active')
	        .siblings('.btn')
	        .removeClass('active');
	    }

	    // filter
	    var name = target.data('name');
	    var option = target.val();
	    this.collection.filterBy(name, function(model){
	      return _.some(model.get('attributes'), function(obj){
	        return obj.name === name && obj.option === option;
	      });
	    });
	    this.ui.add.prop('disabled', this.collection.length !== 1);
	  }

	});

	module.exports = Variations;
	App.prototype.set('POSApp.Products.Variations', Variations);

/***/ },
/* 176 */
/***/ function(module, exports) {

	module.exports = "<div class=\"popover-body\">\n{{#variations}}\n  <strong>{{name}}</strong>\n  {{#unless this.select}}\n    <div class=\"btn-group\" data-toggle=\"buttons\">\n    {{#each options}}\n      <button class=\"btn btn-default\" value=\"{{this}}\" data-name=\"{{../name}}\">{{this}}</button>\n    {{/each}}\n    </div>\n  {{/unless}}\n  {{#if this.select}}\n    <select data-name=\"{{name}}\">\n      <option value=\"\">{{this.emptyOption}}</option>\n    {{#each options}}\n      <option value=\"{{this}}\">{{this}}</option>\n    {{/each}}\n    </select>\n  {{/if}}\n{{/variations}}\n</div>\n<div class=\"popover-footer\">\n  <button class=\"btn btn-success\" data-action=\"add\" disabled>Add to Cart</button>\n</div>\n"

/***/ },
/* 177 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var CollectionView = __webpack_require__(70);
	var Variation = __webpack_require__(178);
	var _ = __webpack_require__(5);

	var Empty = ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: 'pos.products.empty'
	});

	module.exports = CollectionView.extend({
	  childView: Variation,
	  emptyView: Empty,
	  childViewContainer: 'ul',

	  initialize: function(options){
	    options = options || {};

	    this.collection = this.model.getVariations();
	    this.collection.resetFilters();
	    this.filterVariations(options.filter);
	  },

	  onShow: function() {
	    this.$el.hide().slideDown(250);
	  },

	  filterVariations: function(filter){
	    if(filter){
	      filter = filter || {};
	      var matchMaker = function(model){
	        var attributes = model.get('attributes');
	        return _.any(attributes, function(attribute){
	          return attribute.name === filter.name &&
	            attribute.option === filter.option;
	        });

	      };
	      this.collection.filterBy('variation', matchMaker);
	    }
	  }

	});

/***/ },
/* 178 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var Radio = __webpack_require__(4);

	var Item = ItemView.extend({
	  template: 'pos.products.product',

	  className: 'list-row',

	  ui: {
	    add : 'a[data-action="add"]'
	  },

	  events: {
	    'click @ui.add' : 'addToCart'
	  },

	  // todo: why is this necessary?
	  modelEvents: {
	    'change:stock_quantity': 'render'
	  },

	  addToCart: function(e){
	    e.preventDefault();
	    Radio.request('router', 'add:to:cart', {model: this.model});
	  },

	  templateHelpers: function(){
	    return {
	      variation: true,
	      title: this.options.model.parent.get('title')
	    };
	  }

	});

	module.exports = Item;
	App.prototype.set('POSApp.Products.Item.Drawer.Variation', Item);

/***/ },
/* 179 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var _ = __webpack_require__(5);

	var View = ItemView.extend({
	  template: 'pagination',

	  //ui: {
	  //  prev    : '.prev',
	  //  next    : '.next'
	  //},
	  //
	  //events: {
	  //  'click @ui.prev': 'onPrev',
	  //  'click @ui.next': 'onNext'
	  //},

	  // todo: improve this
	  collectionEvents: {
	    'add remove paginated:change:page paginated:change:numPages reset':
	      _.debounce(function(){
	        if(!this.isDestroyed){
	          this.render();
	        }
	      }, 10)
	  },

	  initialize: function(){
	    this.listenTo(this.collection.superset(), {
	      'end:fullSync': this.render
	    });
	  },

	  templateHelpers: function(){
	    var queue = this.collection.superset().queue.length;
	    return {
	      showing : this.collection.length,
	      local   : this.collection.superset().length,
	      queue   : queue,
	      hasQueue: queue > 0
	    };
	  }

	  //onRender: function() {
	  //  this.$('.current').text(this.collection.getPage() + 1);
	  //  this.$('.total').text(this.collection.getNumPages());
	  //  this.$('.prev').prop('disabled', !this.collection.hasPrevPage());
	  //  this.$('.next').prop('disabled', !this.collection.hasNextPage());
	  //},

	  //onPrev: function() {
	  //  if (this.collection.hasPrevPage()) {
	  //    this.collection.prevPage();
	  //  }
	  //},
	  //
	  //onNext: function() {
	  //  if (this.collection.hasNextPage()) {
	  //    this.collection.nextPage();
	  //  }
	  //}
	});

	module.exports = View;
	App.prototype.set('Components.Pagination.View', View);

/***/ },
/* 180 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	var LayoutView = __webpack_require__(181);
	var ItemsView = __webpack_require__(182);
	var TotalsView = __webpack_require__(187);
	var NotesView = __webpack_require__(188);
	var CustomerView = __webpack_require__(189);
	var Buttons = __webpack_require__(76);
	//var debug = require('debug')('cart');
	var _ = __webpack_require__(5);
	var App = __webpack_require__(2);
	var Utils = __webpack_require__(8);
	var polyglot = __webpack_require__(10);

	var CartRoute = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container  = options.container;
	    this.collection = options.collection;
	    this.setTabLabel({
	      tab   : 'right',
	      label : polyglot.t('titles.cart')
	    });
	  },

	  fetch: function() {
	    if (this.collection.isNew()) {
	      return this.collection.fetch({ silent: true });
	    }
	  },

	  onFetch: function(id){
	    this.order = this.collection.setActiveOrder(id);

	    if(this.order){
	      this.setTabLabel({
	        tab   : 'right',
	        label : this.tabLabel(this.order)
	      });

	      this.listenTo( this.order, 'change:total', function(model){
	        this.setTabLabel({
	          tab   : 'right',
	          label : this.tabLabel(model)
	        });
	      });
	    }
	  },

	  render: function() {
	    this.layout = new LayoutView({
	      order: this.order
	    });

	    this.listenTo( this.layout, 'show', this.onShow );

	    this.container.show( this.layout );
	  },

	  onRender: function(){
	    if( this.order && !this.order.isEditable() ){
	      this.navigate('receipt/' + this.order.id, { trigger: true });
	    }
	  },

	  onShow: function(){
	    if(!this.order){
	      return this.noActiveOrder();
	    }

	    this.showCart();
	    this.showTotals();
	    this.showCustomer();
	    this.showActions();
	    this.showNotes();
	  },

	  /**
	   * Empty Cart
	   */
	  noActiveOrder: function(){
	    var view = new ItemsView();
	    this.layout.getRegion('list').show(view);
	    _.invoke([
	      this.layout.getRegion('totals'),
	      this.layout.getRegion('customer'),
	      this.layout.getRegion('actions'),
	      this.layout.getRegion('note')
	    ], 'empty');
	  },

	  /**
	   * Cart Items
	   */
	  showCart: function() {
	    var view = new ItemsView({
	      collection: this.order.cart
	    });
	    this.layout.getRegion('list').show(view);
	  },

	  /**
	   * Cart Totals
	   */
	  showTotals: function() {
	    var view = new TotalsView({
	      model: this.order
	    });
	    this.on('discount:clicked', view.showDiscountRow);
	    this.layout.getRegion('totals').show(view);
	  },

	  showCustomer: function(){
	    var view = new CustomerView({
	      model: this.order
	    });
	    this.layout.getRegion('customer').show( view );
	  },

	  /**
	   * Actions
	   * - order discount removed in WC 2.3
	   */
	  showActions: function() {
	    var view = new Buttons({
	      buttons: [
	        {action: 'void',      className: 'btn-danger pull-left'},
	        {action: 'fee',       className: 'btn-primary'},
	        {action: 'shipping',  className: 'btn-primary'},
	        //{action: 'discount',  className: 'btn-primary'},
	        {action: 'note',      className: 'btn-primary'},
	        {action: 'checkout',  className: 'btn-success'}
	      ]
	    });

	    this.listenTo(view, {
	      'action:void': function(btn, view){
	        view.triggerMethod('disableButtons');
	        this.layout.getRegion('list').currentView.voidCart( this.order );
	      },
	      'action:note': function(){
	        this.layout.getRegion('note').currentView.showNoteField();
	      },
	      //'action:discount': function(){
	      //  this.layout.getRegion('totals').currentView.showDiscountRow();
	      //},
	      'action:fee': function(){
	        this.order.cart.addToCart({
	          type  : 'fee',
	          title : polyglot.t('titles.fee')
	        });
	      },
	      'action:shipping': function(){
	        this.order.cart.addToCart({
	          type        : 'shipping',
	          method_title: polyglot.t('titles.shipping'),
	          method_id   : '' // todo: settings
	        });
	      },
	      'action:checkout': function(){
	        this.navigate('checkout/' + this.order.id, { trigger: true });
	      }
	    });

	    this.layout.getRegion('actions').show( view );
	  },

	  /**
	   * Order Notes
	   */
	  showNotes: function() {
	    var view = new NotesView({
	      model: this.order
	    });
	    this.on( 'note:clicked', view.showNoteField );
	    this.layout.getRegion('note').show( view );
	  },

	  tabLabel: function(order){
	    var prefix = polyglot.t('titles.cart'),
	      total = order.get('total'),
	      formatTotal = Utils.formatMoney(total);

	    return prefix + ': ' + formatTotal;
	  }

	});

	module.exports = CartRoute;
	App.prototype.set('POSApp.Cart.Route', CartRoute);

/***/ },
/* 181 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);

	module.exports = LayoutView.extend({
	  template: 'pos.cart.panel',

	  initialize: function(options){
	    options = options || {};
	    this.order = options.order;
	  },

	  regions: {
	    list      : '.cart-list',
	    totals    : '.cart-totals',
	    customer  : '.cart-customer',
	    actions   : '.cart-actions',
	    note      : '.cart-notes',
	    footer    : '.cart-footer'
	  },

	  attributes: {
	    'class'   : 'panel cart'
	  },

	  /**
	   * add/remove cart-empty class
	   */
	  onShow: function(){
	    this.$el.toggleClass('cart-empty', !this.order);
	  }

	});

/***/ },
/* 182 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var CollectionView = __webpack_require__(70);
	var LineItem = __webpack_require__(183);
	var App = __webpack_require__(2);
	var polyglot = __webpack_require__(10);

	var Empty = ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return polyglot.t('messages.cart-empty');
	  }
	});

	var View = CollectionView.extend({
	  tagName: 'ul',
	  childView: LineItem,
	  emptyView: Empty,
	  voidCart: function( order ){
	    this.children.each(function(child){
	      if( child instanceof Empty ){
	        order.destroy();
	      } else {
	        child.getRegion('item').currentView.removeItem();
	      }
	    });
	  }
	});

	module.exports = View;
	App.prototype.set('POSApp.Cart.Views.Items', View);

/***/ },
/* 183 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var ItemView = __webpack_require__(184);
	var DrawerView = __webpack_require__(186);
	var $ = __webpack_require__(6);

	/**
	 * @todo Abstract ListItemView
	 */

	/* jshint -W071 */
	$.fn.selectText = function(){
	  var range, element = this[0];
	  if (document.body.createTextRange) {
	    range = document.body.createTextRange();
	    range.moveToElementText(element);
	    range.select();
	  } else if (window.getSelection) {
	    var selection = window.getSelection();
	    range = document.createRange();
	    range.selectNodeContents(element);
	    selection.removeAllRanges();
	    selection.addRange(range);
	  }
	};
	/* jshint +W071 */

	module.exports = LayoutView.extend({

	  tagName: 'li',

	  className: function() { return this.model.get('type'); },

	  template: function() {
	    return '<div class="list-item cart-item"></div>' +
	      '<div class="list-drawer cart-drawer"></div>';
	  },

	  regions: {
	    item    : '.list-item',
	    drawer  : '.list-drawer'
	  },

	  modelEvents: {
	    'pulse': 'pulse'
	  },

	  onRender: function(){
	    var view = new ItemView({ model: this.model });

	    this.listenTo( view, 'drawer:open', this.openDrawer );
	    this.listenTo( view, 'drawer:close', this.closeDrawer );
	    this.listenTo( view, 'drawer:toggle', this.toggleDrawer );

	    this.getRegion('item').show(view);
	  },

	  openDrawer: function(){
	    var view = new DrawerView({ model: this.model });
	    this.getRegion('drawer').show(view);
	    this.$el.addClass('drawer-open');
	  },

	  closeDrawer: function(){
	    this.getRegion('drawer').empty();
	    this.$el.removeClass('drawer-open');
	  },

	  toggleDrawer: function(){
	    if( this.getRegion('drawer').hasView() ){
	      this.closeDrawer();
	    } else {
	      this.openDrawer();
	    }
	  },

	  pulse: function(opt) {
	    if(opt === 'remove'){ return; }
	    var el = this.$el, type = this.model.get( 'type' );

	    // scroll to row
	    el.addClass('pulse-in')
	      .scrollIntoView({ complete: function(){
	        el.animate({backgroundColor: 'transparent'}, 500, function() {
	          $(this).removeClass('pulse-in').removeAttr('style');
	          if( type === 'fee' || type === 'shipping' ) {
	            $('.title strong', this).focus().selectText();
	          }
	        });
	      }});

	  }

	});

/***/ },
/* 184 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(88);
	var Utils = __webpack_require__(8);
	var bb = __webpack_require__(16);
	var Radio = bb.Radio;
	var AutoGrow = __webpack_require__(163);
	var Numpad = __webpack_require__(185);
	var _ = __webpack_require__(5);

	module.exports = FormView.extend({
	  template: 'pos.cart.item',

	  className: 'list-row',

	  initialize: function() {
	    this.tax = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tax'
	    }) || {};
	  },

	  templateHelpers: function(){
	    var type = this.model.get('type');
	    return {
	      product: (type !== 'shipping' && type !== 'fee')
	    };
	  },

	  behaviors: {
	    AutoGrow: {
	      behaviorClass: AutoGrow
	    },
	    Numpad: {
	      behaviorClass: Numpad
	    }
	  },

	  ui: {
	    remove  : '*[data-action="remove"]',
	    more    : '*[data-action="more"]',
	    title   : '.title'
	  },

	  events: {
	    'click @ui.remove': 'removeItem',
	    'click @ui.title': 'focusTitle'
	  },

	  triggers: {
	    'click @ui.more'  : 'drawer:toggle'
	  },

	  modelEvents: {
	    'change:title'        : 'save',
	    'change:method_title' : 'save'
	  },

	  bindings: {
	    'input[name="quantity"]' : {
	      observe: 'quantity',
	      onGet: function(value) {
	        return Utils.formatNumber(value, 'auto');
	      },
	      onSet: Utils.unformat
	    },
	    '*[data-name="title"]' : {
	      observe: 'title',
	      events: ['blur']
	    },
	    'dl.meta': {
	      observe: 'meta',
	      updateMethod: 'html',
	      onGet: function(val){
	        var row = '';
	        _.each(val, function(meta){
	          row += '<dt>' + meta.label + ':</dt><dd>' + meta.value + '</dd>';
	        });
	        return row;
	      }
	    },
	    '*[data-name="method_title"]': 'method_title',
	    'input[name="item_price"]': {
	      observe: 'item_price',
	      onGet: Utils.formatNumber,
	      onSet: Utils.unformat
	    },
	    '.total': {
	      observe: ['total', 'subtotal', 'tax_class', 'taxable'],
	      updateMethod: 'html',
	      onGet: function(value) {
	        if( this.tax.tax_display_cart === 'incl' ) {
	          value[0] = this.model.sum(['total', 'total_tax']);
	          value[1] = this.model.sum(['subtotal', 'subtotal_tax']);
	        }

	        var total     = Utils.formatMoney(value[0]),
	            subtotal  = Utils.formatMoney(value[1]);

	        if(total !== subtotal){
	          return '<del>' + subtotal + '</del> <ins>' + total + '</ins>';
	        } else {
	          return total;
	        }
	      }
	    }
	  },

	  save: function(){
	    console.log(arguments);
	    this.model.save();
	  },

	  removeItem: function(e) {
	    if(e) { e.preventDefault(); }
	    var self = this;
	    this.ui.remove.attr('disabled', 'true');
	    this.$el.addClass('pulse-out')
	      .fadeOut(500, function(){
	      self.model.destroy();
	    });
	  },

	  focusTitle: function(){
	    this.ui.title.find('strong').focus();
	  }

	});

/***/ },
/* 185 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(79);
	var App = __webpack_require__(2);
	var Radio = __webpack_require__(4);
	var View = __webpack_require__(160);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);

	var NumpadBehavior = Behavior.extend({

	  ui: {
	    target: '*[data-numpad]'
	  },

	  events: {
	    'click @ui.target'      : 'numpadPopover',
	    'open:numpad @ui.input' : 'numpadPopover'
	  },

	  onRender: function() {
	    if(window.Modernizr.touch) {
	      this.ui.target.each(function(){
	        if( $(this).is('input') ){
	          $(this).attr('readonly', true);
	        }
	      });
	    }
	  },

	  numpadPopover: function(e){
	    var name    = $(e.target).attr('name'),
	        options = _.clone( $(e.target).data() );

	    // numpad
	    _.defaults( options, {
	      value : this.view.model.get( name )
	    });

	    if( _.isString( options.original ) ){
	      options.original = this.view.model.get(options.original);
	    }

	    var numpad = new View(options);

	    this.listenTo(numpad, 'input', function(value){
	      Radio.request('popover', 'close');
	      this.view.model.set( name, value, { numpadChange: true } );
	    });

	    // popover
	    this._numpadPopover(numpad, options, e.target);
	  },

	  _numpadPopover: function(numpad, options, target){
	    _.defaults( options, {
	      target : target,
	      view   : numpad,
	      classes: 'popover-theme-arrows popover-numpad'
	    });

	    Radio.request('popover', 'open', options);

	    // @todo: remove this hack, popover & numpad should be uncoupled
	    numpad.$('input').focus().select();
	  }

	});

	module.exports = NumpadBehavior;
	App.prototype.set('Behaviors.Numpad', NumpadBehavior);

/***/ },
/* 186 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(88);
	var Utils = __webpack_require__(8);
	var AutoGrow = __webpack_require__(163);
	var Numpad = __webpack_require__(185);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var bb = __webpack_require__(16);
	var Radio = bb.Radio;

	module.exports = FormView.extend({

	  template: 'pos.cart.item-drawer',

	  behaviors: {
	    AutoGrow: {
	      behaviorClass: AutoGrow
	    },
	    Numpad: {
	      behaviorClass: Numpad
	    }
	  },

	  ui: {
	    addMeta     : '*[data-action="add-meta"]',
	    removeMeta  : '*[data-action="remove-meta"]',
	    metaLabel   : 'input[name="meta.label"]',
	    metaValue   : 'textarea[name="meta.value"]'
	  },

	  events: {
	    'click @ui.addMeta'     : 'addMetaFields',
	    'click @ui.removeMeta'  : 'removeMetaFields',
	    'blur @ui.metaLabel'    : 'updateMeta',
	    'blur @ui.metaValue'    : 'updateMeta'
	  },

	  modelEvents: {
	    'pulse': 'pulse'
	  },

	  bindings: {
	    'input[name="regular_price"]' : {
	      observe: 'regular_price',
	      onGet: Utils.formatNumber,
	      onSet: Utils.unformat
	    },
	    'input[name="taxable"]' : 'taxable',
	    'select[name="tax_class"]' : {
	      observe: 'tax_class',
	      selectOptions: {
	        collection: function(){
	          return Radio.request('entities', 'get', {
	            type: 'option',
	            name: 'tax_classes'
	          });
	        }
	      },
	      attributes: [{
	        name: 'disabled',
	        observe: 'taxable',
	        onGet: function(val) {
	          return !val;
	        }
	      }]
	    },
	    'select[name="method_id"]': {
	      observe: 'method_id',
	      selectOptions: {
	        collection: function(){
	          return Radio.request('entities', 'get', {
	            type: 'option',
	            name: 'shipping'
	          });
	        },
	        comparator: function(){}
	      }
	    }
	  },

	  onShow: function() {
	    this.$el.hide().slideDown(250);
	  },

	  remove: function() {
	    this.$el.slideUp( 250, function() {
	      FormView.prototype.remove.call(this);
	    }.bind(this));
	  },

	  pulse: function(type){
	    if(type === 'remove'){
	      return this.$el.slideUp(250);
	    }
	  },

	  updateMeta: function(e) {
	    var el        = $(e.target),
	        name      = el.attr('name').split('.'),
	        attribute = name[0],
	        value     = el.val(),
	        data      = {},
	        self      = this;

	    var newValue = function(){
	      var key = el.parent('span').data('key');
	      var meta = ( self.model.get('meta') || [] );

	      // select row (or create new one)
	      var row = _.where( meta, { 'key': key } );
	      row = row.length > 0 ? row[0] : { 'key': key };
	      row[ name[1] ] = value;

	      // add the change row and make unique on key
	      meta.push(row);
	      return _.uniq( meta, 'key' );
	    };

	    if( name.length > 1 && attribute === 'meta' ) {
	      value = newValue();
	    }

	    data[ attribute ] = value;

	    this.model.save(data);
	  },

	  addMetaFields: function(e){
	    e.preventDefault();

	    var row = $(e.currentTarget).prev('span').data('key');
	    var i = row ? row + 1 : 1 ;

	    $('<span data-key="'+ i +'" />')
	      .append('' +
	        '<input class="form-control" type="text" name="meta.label">' +
	        '<textarea class="form-control" name="meta.value"></textarea>' +
	        '<a href="#" data-action="remove-meta">' +
	        '<i class="icon-remove icon-lg"></i>' +
	        '</a>'
	      )
	      .insertBefore( $(e.currentTarget) );
	  },

	  removeMetaFields: function(e){
	    e.preventDefault();

	    var row = $(e.currentTarget).parent('span'),
	        meta = ( this.model.get('meta') || [] ),
	        index = _.findIndex( meta, { 'key': row.data('key') } );

	    if( index >= 0 ){
	      meta.splice( index, 1 );
	      this.model.save({ 'meta': meta });
	      this.model.trigger('change:meta');
	    }

	    row.remove();
	  }

	});

/***/ },
/* 187 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(88);
	var Radio = __webpack_require__(4);

	module.exports = ItemView.extend({
	  tagName: 'ul',
	  template: 'pos.cart.totals',

	  initialize: function() {
	    this.tax = Radio.request('entities', 'get', {
	      type : 'option',
	      name : 'tax'
	    }) || {};
	  },

	  // todo: why is this necessary?!
	  // view should re-render automatically on model change
	  modelEvents: {
	    'change': 'render'
	  },

	  /**
	   *
	   */
	  templateHelpers: function(){
	    var data = {
	      itemized: this.tax.tax_total_display === 'itemized',
	      has_discount: 0 !== this.model.get('cart_discount')
	    };

	    if( this.tax.tax_display_cart === 'incl' ) {
	      data.subtotal = this.model.sum(['subtotal', 'subtotal_tax']);
	      data.cart_discount = this.model.sum(
	        ['cart_discount', 'cart_discount_tax']
	      );
	      data.incl_tax = true;
	    }

	    return data;
	  }

	});

/***/ },
/* 188 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var _ = __webpack_require__(5);

	module.exports = ItemView.extend({
	  template: _.template( '<%= note %>' ),

	  modelEvents: {
	    'change:note': 'render'
	  },

	  events: {
	    'click'   : 'edit',
	    'keypress'  : 'saveOnEnter',
	    'blur'    : 'save'
	  },

	  onShow: function() {
	    this.showOrHide();
	  },

	  showOrHide: function() {
	    if( this.model.get('note') === '' ) {
	      this.$el.hide();
	    }
	  },

	  edit: function() {
	    this.$el.attr('contenteditable','true').focus();
	  },

	  save: function() {
	    var value = this.$el.text();

	    // validate and save
	    this.model.save({ note: value });
	    this.$el.attr('contenteditable','false');
	    this.showOrHide();
	  },

	  saveOnEnter: function(e) {
	    // save note on enter
	    if (e.which === 13) {
	      e.preventDefault();
	      this.$el.blur();
	    }
	  },

	  showNoteField: function() {
	    this.$el.show();
	    this.$el.attr('contenteditable','true').focus();
	  }
	});

/***/ },
/* 189 */
/***/ function(module, exports, __webpack_require__) {

	var View = __webpack_require__(61);
	var App = __webpack_require__(2);
	var _ = __webpack_require__(5);
	var Radio = __webpack_require__(4);
	var Customers = __webpack_require__(190);
	var Dropdown = __webpack_require__(152);
	var Mn = __webpack_require__(3);

	var View = View.extend({
	  template: 'pos.cart.customer',

	  className: 'list-row',

	  initialize: function(){
	    this.customers = Radio.request('entities', 'get', {
	      type: 'filtered',
	      name: 'customers',
	      perPage : 10
	    });

	    this.mergeOptions({
	      guest: this.customers.superset().getGuestCustomer()
	    }, ['guest']);

	    /**
	     * @todo customer attr relation direct to customer model
	     */
	    this.listenTo(this.customers.superset(), 'modal:save', this.onModalSave);

	    _.bindAll( this, 'dropdownContent' );
	  },

	  behaviors: {
	    Dropdown: {
	      behaviorClass: Dropdown
	    }
	  },

	  ui: {
	    searchField     : 'input',
	    removeCustomer  : '*[data-action="remove"]'
	  },

	  events: {
	    'input @ui.searchField'   : 'query',
	    'click @ui.removeCustomer': 'removeCustomer'
	  },

	  query: function(){
	    var value = this.ui.searchField.val();
	    this._query(value);
	  },

	  _query: _.debounce( function(value){
	    this.customers
	      .query(value)
	      .firstPage();
	  }, 149),

	  modelEvents: {
	    'change:customer': 'render'
	  },

	  dropdownContent: function( drop ){
	    this.customersRegion = new Mn.Region({
	      el: drop.content
	    });

	    // reposition on filter
	    this.listenTo( this.customers, 'reset', function(){
	      drop.position.call(drop);
	    });

	    // reposition on show
	    this.listenTo( this.customersRegion, 'show', function(){
	      drop.position.call(drop);
	    });

	    return ''; // return empty content
	  },

	  onDropdownOpen: function(){
	    var view = new Customers({
	      collection: this.customers,
	      filter: this.$('input').val()
	    });

	    this.listenTo(view, 'childview:customer:selected', function(view){
	      this.saveCustomer( view.model.toJSON() );
	    });

	    this.customersRegion.show(view);
	  },

	  onDropdownClose: function(){
	    this.customersRegion.empty();
	  },

	  onTargetKeydown: function(e){
	    this.customersRegion.currentView.moveFocus(e.which);
	  },

	  removeCustomer: function(){
	    this.saveCustomer( this.getOption('guest') );
	  },

	  saveCustomer: function(customer){
	    this.model.unset('customer', { silent: true });
	    this.model.save({
	      customer_id: customer.id,
	      customer: customer
	    });
	  }

	});

	module.exports = View;
	App.prototype.set('POSApp.Cart.Views.Customer', View);

/***/ },
/* 190 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var InfiniteListView = __webpack_require__(172);
	var polyglot = __webpack_require__(10);
	var Tmpl = __webpack_require__(191);
	var hbs = __webpack_require__(7);
	var _ = __webpack_require__(5);

	var Customer = ItemView.extend({
	  template: hbs.compile(Tmpl),
	  tagName: 'li',
	  triggers: {
	    'click': 'customer:selected'
	  },
	  addFocus: function(){
	    this.$el.addClass('focus').scrollIntoView();
	  },
	  removeFocus: function(){
	    this.$el.removeClass('focus');
	  },
	  hasFocus: function(){
	    return this.$el.hasClass('focus');
	  }
	});

	var NoCustomer = ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return polyglot.t('messages.no-customer');
	  }
	});

	var Customers = InfiniteListView.extend({
	  childView: Customer,
	  emptyView: NoCustomer,
	  childViewContainer: 'ul',
	  className: 'list-infinite dropdown-list',

	  initialize: function(options){
	    options = options || {};
	    var filtered = this.collection;
	    var customers = this.collection.superset();

	    if( customers.isNew() ){
	      return customers.fetch()
	        .then(function(){
	          customers.fullSync();
	        });
	    } else {
	      filtered.query(options.filter);
	    }
	  },

	  childEvents: {
	    focus: 'onChildFocus'
	  },

	  onChildFocus: function(){
	    this.children.each(function(view){
	      view.removeFocus();
	    });
	  },

	  moveFocus: function(keyCode) {
	    if(keyCode === 40){
	      this.moveFocusDown();
	    }
	    if(keyCode === 38){
	      this.moveFocusUp();
	    }
	    if(keyCode === 13){
	      this.getFocusedChild().trigger('customer:selected');
	    }
	  },

	  moveFocusDown: function(){
	    var next, nextChild, focused = this.getFocusedChild();
	    if(!focused){
	      return this.children.first().addFocus();
	    }

	    _.each(this.children._views, function(child){
	      if(next){
	        nextChild = child;
	        next = false;
	      }
	      next = child === focused;
	    });

	    if(nextChild){
	      focused.removeFocus();
	      nextChild.addFocus();
	    }
	  },

	  moveFocusUp: function(){
	    var next, nextChild, focused = this.getFocusedChild();
	    if(!focused){
	      return this.children.last().addFocus();
	    }

	    _.eachRight(this.children._views, function(child){
	      if(next){
	        nextChild = child;
	        next = false;
	      }
	      next = child === focused;
	    });

	    if(nextChild){
	      focused.removeFocus();
	      nextChild.addFocus();
	    }
	  },

	  getFocusedChild: function(){
	    return this.children.find(function(view){
	      return view.hasFocus();
	    });
	  }

	});


	module.exports = Customers;

/***/ },
/* 191 */
/***/ function(module, exports) {

	module.exports = "<strong>{{formatCustomerName this}}</strong>\n<small>#{{id}}{{#if email}} - {{email}}{{/if}}</small>"

/***/ },
/* 192 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	//var debug = require('debug')('checkout');
	var App = __webpack_require__(2);
	var LayoutView = __webpack_require__(193);
	var StatusView = __webpack_require__(194);
	var GatewaysView = __webpack_require__(196);
	var polyglot = __webpack_require__(10);
	var Radio = __webpack_require__(4);

	var CheckoutRoute = Route.extend({

	  initialize: function(options){
	    options = options || {};
	    this.container = options.container;
	    this.collection = options.collection;
	    this.setTabLabel({
	      tab   : 'right',
	      label : polyglot.t('titles.checkout')
	    });
	  },

	  fetch: function(){
	    if(this.collection.isNew()){
	      return this.collection.fetch();
	    }
	  },

	  onFetch: function(id){
	    this.order = this.collection.setActiveOrder(id);

	    if(this.order){
	      this.listenTo( this.order, 'change:status', function(model){
	        if(!model.isEditable() && model.get('status') !== 'failed'){
	          this.navigate('receipt/' + model.id, { trigger: true });
	        }
	      });
	    }
	  },

	  render: function(){
	    this.layout = new LayoutView();

	    this.listenTo( this.layout, 'show', function(){
	      this.showStatus();
	      this.showGateways();
	      this.showActions();
	    });

	    this.container.show(this.layout);
	  },

	  onRender: function(){
	    if( this.order && !this.order.isEditable() ){
	      this.navigate('receipt/' + this.order.id, { trigger: true });
	    }
	  },

	  showStatus: function(){
	    var view = new StatusView({
	      model: this.order
	    });
	    this.layout.getRegion('status').show(view);
	  },

	  showGateways: function(){
	    this.order.gateways.order = this.order;
	    var view = new GatewaysView({
	      collection: this.order.gateways
	    });
	    this.layout.getRegion('list').show(view);
	  },

	  showActions: function(){
	    var view = Radio.request('buttons', 'view', {
	      buttons: [{
	        action: 'return-to-sale',
	        className: 'btn pull-left'
	      },{
	        action: 'process-payment',
	        className: 'btn btn-success',
	        icon: 'prepend'
	      }]
	    });

	    this.listenTo(view, {
	      'action:return-to-sale': function(){
	        this.navigate('cart/' + this.order.id, { trigger: true });
	      },
	      'action:process-payment': function(btn){
	        btn.trigger('state', 'loading');
	        this.order.process()
	          .always(function(){
	            btn.trigger('state', 'reset');
	          });
	      }
	    });

	    this.layout.getRegion('actions').show(view);
	  }

	});

	module.exports = CheckoutRoute;
	App.prototype.set('POSApp.Checkout.Route', CheckoutRoute);

/***/ },
/* 193 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var App = __webpack_require__(2);
	var hbs = __webpack_require__(7);

	var Layout = LayoutView.extend({

	  initialize: function(){
	    this.template = hbs.compile('' +
	      '<div class="panel-header checkout-status"></div>' +
	      '<div class="panel-body list checkout-list"></div>' +
	      '<div class="list-actions checkout-actions"></div>'
	    );
	  },

	  regions: {
	    status  : '.checkout-status',
	    list    : '.checkout-list',
	    actions : '.checkout-actions'
	  },

	  attributes: {
	    'class' : 'panel checkout'
	  }

	});

	module.exports = Layout;
	App.prototype.set('POSApp.Checkout.Views.Layout', Layout);

/***/ },
/* 194 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var hbs = __webpack_require__(7);
	//var $ = require('jquery');
	var polyglot = __webpack_require__(10);
	var Tmpl = __webpack_require__(195);
	var _ = __webpack_require__(5);

	var View = ItemView.extend({
	  template: hbs.compile(Tmpl),

	  initialize: function(){

	  },

	  templateHelpers: function(){
	    return {
	      status: this.getStatus(),
	      message: this.getMessage()
	    };
	  },

	  modelEvents: {
	    'change:payment_details': 'render'
	  },

	  getStatus: function(){
	    if(this.model.get('payment_details.paid') === false){
	      this.$el.addClass('fail');
	      return polyglot.t('titles.unpaid');
	    }
	    return polyglot.t('titles.to-pay');
	  },

	  getMessage: function(){
	    var messages = this.model.get('payment_details.message');
	    if( _.isArray(messages) ){
	      return _.last(messages);
	    }
	    return messages;
	  }

	});

	module.exports = View;
	App.prototype.set('POSApp.Checkout.Views.Status', View);

/***/ },
/* 195 */
/***/ function(module, exports) {

	module.exports = "<h4>{{status}}: {{{money total}}}</h4>\n{{#if message}}\n  <p>{{{message}}}</p>\n{{/if}}"

/***/ },
/* 196 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var CollectionView = __webpack_require__(70);
	var Gateway = __webpack_require__(197);
	var App = __webpack_require__(2);
	var polyglot = __webpack_require__(10);

	var EmptyView = ItemView.extend({
	  tagName: 'li',
	  className: 'empty',
	  template: function(){
	    return polyglot.t('messages.no-gateway');
	  }
	});

	var View = CollectionView.extend({
	  tagName: 'ul',
	  childView: Gateway,
	  emptyView: EmptyView
	});

	module.exports = View;
	App.prototype.set('POSApp.Checkout.Views.Gateways', View);

/***/ },
/* 197 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var GatewayView = __webpack_require__(198);
	var DrawerView = __webpack_require__(199);

	module.exports = LayoutView.extend({
	  tagName: 'li',

	  template: function() {
	    return '<div class="gateway"></div><div class="drawer"></div>';
	  },

	  regions: {
	    gatewayRegion: '.gateway',
	    drawerRegion: '.drawer'
	  },

	  initialize: function(){
	    this.listenTo(this.model, 'change:active', this.toggleDrawer);
	  },

	  onRender: function(){
	    var view = new GatewayView({
	      model: this.model
	    });
	    this.gatewayRegion.show(view);
	  },

	  onShow: function(){
	    if(this.model.get('active')){
	      this.openDrawer();
	    }
	  },

	  openDrawer: function(){
	    var view = new DrawerView({
	      model: this.model
	    });
	    this.drawerRegion.show(view);
	    this.$el.addClass('active');
	  },

	  closeDrawer: function(){
	    this.drawerRegion.empty();
	    this.$el.removeClass('active');
	  },

	  toggleDrawer: function(){
	    if( this.drawerRegion.hasView() ){
	      this.closeDrawer();
	    } else {
	      this.openDrawer();
	    }
	  }
	});

/***/ },
/* 198 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var hbs = __webpack_require__(7);
	//var $ = require('jquery');

	module.exports = ItemView.extend({
	  tagName: 'h5',
	  template: hbs.compile('' +
	    '{{method_title}}' +
	    '{{#if icon}}<img src="{{icon}}">{{/if}}'
	  ),

	  events: {
	    'click': 'makeActive'
	  },

	  makeActive: function(){
	    this.model.set({
	      active: true
	    });
	  }
	});

/***/ },
/* 199 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(88);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	var Numpad = __webpack_require__(185);
	var Utils = __webpack_require__(8);
	var polyglot = __webpack_require__(10);

	module.exports = FormView.extend({

	  initialize: function() {
	    this.template = 'pos.checkout.gateways.' + this.model.id;
	    this.order_total = this.model.collection.order.get('total');
	    this.updateStatusMessage();
	  },

	  templateHelpers: function(){
	    return {
	      total: this.order_total
	    };
	  },

	  behaviors: {
	    Numpad: {
	      behaviorClass: Numpad
	    }
	  },

	  modelEvents: {
	    'change:message': 'updateStatusMessage'
	  },

	  onRender: function(){
	    var self = this;

	    if(this.model.id === 'pos_cash'){
	      return this.posCashRender();
	    }

	    if(this.model.id === 'pos_card'){
	      return this.posCardRender();
	    }

	    /**
	     * bind form elements
	     */
	    this.$('input, select, textarea').each(function(){
	      var name = $(this).attr('name'),
	        id = $(this).attr('id'),
	        data = $(this).data();

	      if(name){
	        return self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	      if(id){
	        return self.addBinding(null, '#' + id, id);
	      }
	      if(_.size(data) === 1){
	        var prop = Object.keys(data)[0];
	        return self.addBinding(
	          null,
	          '*[data-' + prop + '="' + data[prop] + '"]',
	          data[prop]
	        );
	      }

	    });
	  },

	  posCashRender: function(){
	    this.addBinding(null, {
	      '#pos-cash-tendered': {
	        observe: 'pos-cash-tendered',
	        onGet: function(value){
	          this.calcChange(value);
	          return Utils.formatNumber(value);
	        },
	        onSet: function(value){
	          var val = Utils.unformat(value);
	          this.calcChange(val);
	          return val;
	        },
	        initialize: function($el, model){
	          if(!model.get('pos-cash-tendered')){
	            model.set({ 'pos-cash-tendered': this.order_total });
	          }
	        }
	      }
	    });
	  },

	  posCardRender: function(){
	    this.addBinding(null, {
	      '#pos-cashback': {
	        observe: 'pos-cashback',
	        onGet: Utils.formatNumber,
	        onSet: Utils.unformat
	      }
	    });
	  },

	  onShow: function() {
	    this.$el.hide().slideDown(250);

	    if(window.Modernizr.touch){
	      this.$('#pos-cash-tendered').attr('readonly', true);
	      this.$('#pos-cashback').attr('readonly', true);
	    }
	  },

	  remove: function() {
	    this.$el.slideUp( 250, function() {
	      FormView.prototype.remove.call(this);
	    }.bind(this));
	  },

	  calcChange: function(tendered){
	    var change = tendered - this.order_total;
	    var msg = polyglot.t('titles.change') + ': ' + Utils.formatMoney(change);
	    this.model.set({ message: msg, 'pos-cash-change': change });
	  },

	  updateStatusMessage: function(){
	    this.model.collection.order.set({
	      'payment_details.message': this.model.get('message')
	    });
	  }

	});

/***/ },
/* 200 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	var Radio = __webpack_require__(4);
	//var debug = require('debug')('receipt');
	var App = __webpack_require__(2);
	var LayoutView = __webpack_require__(201);
	var StatusView = __webpack_require__(202);
	var ItemsView = __webpack_require__(204);
	var TotalsView = __webpack_require__(206);
	var EmailView = __webpack_require__(207);
	var polyglot = __webpack_require__(10);
	var Buttons = __webpack_require__(76);
	var $ = __webpack_require__(6);

	var ReceiptRoute = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.collection = options.collection;
	    this.autoPrint = options.autoPrint;
	    this.setTabLabel({
	      tab   : 'right',
	      label : polyglot.t('titles.receipt')
	    });
	    this.tax = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'tax'
	    }) || {};
	  },

	  fetch: function() {
	    if(this.collection.isNew()){
	      return this.collection.fetch();
	    }
	  },

	  onFetch: function(id){
	    this.order = this.collection.get(id);
	    this.order.clearCart();

	    // redirect, ie: offsite payment
	    var redirect = this.order.get('payment_details.redirect');
	    if(redirect && redirect !== ''){
	      window.open(redirect, '_blank');
	    }

	    // update products
	    var products = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: 'products'
	    });
	    products.fetchUpdated();
	  },

	  render: function() {
	    this.layout = new LayoutView({
	      model: this.order
	    });

	    this.listenTo( this.layout, 'show', function() {
	      this.showStatus();
	      this.showItems();
	      this.showTotals();
	      this.showActions();
	      if(this.autoPrint){
	        this.print();
	      }
	    });

	    this.container.show( this.layout );
	  },

	  showStatus: function(){
	    var view = new StatusView({
	      model: this.order
	    });
	    this.layout.getRegion('status').show(view);
	  },

	  showItems: function(){
	    var view = new ItemsView({
	      model: this.order
	    });

	    this.layout.getRegion('list').show(view);
	  },

	  showTotals: function(){
	    var view = new TotalsView({
	      model: this.order
	    });

	    this.layout.getRegion('totals').show(view);
	  },

	  showActions: function(){
	    var view = new Buttons({
	      buttons: [{
	        action: 'print',
	        className: 'btn-primary pull-left'
	      }, {
	        action: 'email',
	        className: 'btn-primary pull-left'
	      }, {
	        action: 'new-order',
	        className: 'btn-success'
	      }]
	    });

	    this.listenTo(view, {
	      'action:print': this.print,
	      'action:email': this.email,
	      'action:new-order': function(){
	        this.navigate('', { trigger: true });
	      }
	    });

	    this.layout.getRegion('actions').show(view);
	  },

	  print: function(){
	    Radio.request('print', 'print', {
	      template: 'receipt',
	      model: this.order
	    });
	  },

	  email: function(){
	    var self = this;

	    var view = new EmailView({
	      order_id: this.order.get('id'),
	      email: this.order.get('customer.email')
	    });

	    Radio.request('modal', 'open', view)
	      .then(function(args){
	        var buttons = args.view.getButtons();
	        self.listenTo(buttons, 'action:send', function(btn, view){
	          var email = args.view.getRegion('content').currentView.getEmail();
	          self.send(btn, view, email);
	        });
	      });

	  },

	  // todo: refactor
	  send: function(btn, view, email){
	    var order_id = this.order.get('id'),
	        ajaxurl = Radio.request('entities', 'get', {
	          type: 'option',
	          name: 'ajaxurl'
	        });

	    btn.trigger('state', [ 'loading', '' ]);

	    function onSuccess(resp){
	      if(resp.result === 'success'){
	        btn.trigger('state', [ 'success', resp.message ]);
	      } else {
	        btn.trigger('state', [ 'error', resp.message ]);
	      }
	    }

	    function onError(jqxhr){
	      var message = null;
	      if(jqxhr.responseJSON && jqxhr.responseJSON.errors){
	        message = jqxhr.responseJSON.errors[0].message;
	      }
	      btn.trigger('state', ['error', message]);
	    }

	    $.getJSON( ajaxurl, {
	      action: 'wc_pos_email_receipt',
	      order_id: order_id,
	      email : email
	    })
	    .done(onSuccess)
	    .fail(onError);
	  }

	});

	module.exports = ReceiptRoute;
	App.prototype.set('POSApp.Receipt.Route', ReceiptRoute);

/***/ },
/* 201 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);

	module.exports = LayoutView.extend({
	  template: 'pos.receipt.panel',

	  regions: {
	    status  : '.receipt-status',
	    list    : '.receipt-list',
	    totals  : '.receipt-totals',
	    actions : '.receipt-actions'
	  },

	  className: function(){
	    return 'panel receipt ' + this.model.get('status');
	  }

	});

/***/ },
/* 202 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var hbs = __webpack_require__(7);
	//var $ = require('jquery');
	var polyglot = __webpack_require__(10);
	var Tmpl = __webpack_require__(203);
	var _ = __webpack_require__(5);

	var View = ItemView.extend({

	  template: hbs.compile(Tmpl),

	  className: function(){
	    return this.model.get('payment_details.paid') ? 'paid' : 'unpaid';
	  },

	  initialize: function(){
	    var status = this.model.get('payment_details.paid') ? 'paid' : 'unpaid';
	    this.status = polyglot.t('titles.' + status);

	    var message = this.model.get('payment_details.message');
	    if( _.isArray(message) ){
	      this.message = _.last(message);
	    } else {
	      this.message = message;
	    }
	  },

	  templateHelpers: function(){
	    return {
	      status: this.status,
	      message: this.message
	    };
	  }

	});

	module.exports = View;
	App.prototype.set('POSApp.Receipt.Views.Status', View);

/***/ },
/* 203 */
/***/ function(module, exports) {

	module.exports = "<h4>{{status}}: {{{money total}}}</h4>\n{{#if message}}\n  <p>{{{message}}}</p>\n{{/if}}"

/***/ },
/* 204 */
/***/ function(module, exports, __webpack_require__) {

	var ReceiptView = __webpack_require__(205);
	var App = __webpack_require__(2);

	var View = ReceiptView.extend({
	  tagName: 'ul',
	  template: 'pos.receipt.items'
	});

	module.exports = View;
	App.prototype.set('POSApp.Receipt.Views.Items', View);

/***/ },
/* 205 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Prepare order JSON for display on receipts
	 */
	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var Radio = __webpack_require__(4);
	var _ = __webpack_require__(5);

	/* jshint  -W101, -W071, -W074 */
	module.exports = App.prototype.ReceiptView = ItemView.extend({

	  viewOptions: ['data'],

	  initialize: function() {
	    var tax = Radio.request('entities', 'get', {
	        type: 'option',
	        name: 'tax'
	      }) || {};
	    var data = this.prepare( this.model.toJSON(), tax );
	    this.mergeOptions({ data: data }, this.viewOptions);
	  },

	  templateHelpers: function(){
	    return this.data;
	  },

	  prepare: function(data, settings){
	    data.incl_tax = settings.tax_display_cart === 'incl';
	    data.itemized = settings.tax_total_display === 'itemized';

	    /**
	     * Line Items
	     */
	      // - add regular_price
	    _.each( data.line_items, function(item) {
	      item.on_sale = item.subtotal !== item.total;
	      if(!item.regular_price){
	        var quantity = item.quantity || 1;
	        item.regular_price = parseFloat(item.subtotal) / quantity;
	      }
	    });

	    if( data.incl_tax ) {
	      _.each( data.line_items, function(item) {
	        var quantity = item.quantity || 1;
	        item.subtotal = parseFloat(item.subtotal) + parseFloat(item.subtotal_tax);
	        item.total = parseFloat(item.total) + parseFloat(item.total_tax);
	        item.regular_price = item.subtotal / quantity;
	        item.price = item.total / quantity;
	      });
	    }

	    /**
	     * Totals
	     */
	    if( data.itemized ){
	      _.each( data.tax_lines, function(line) {
	        line.has_tax = 0 !== parseFloat(line.total);
	      });
	    }

	    // WC 2.3 changed cart_discount to total_discount
	    data.cart_discount = data.cart_discount || data.total_discount;

	    if( data.incl_tax ) {
	      data.subtotal = parseFloat(data.subtotal) + parseFloat(data.subtotal_tax);
	      data.cart_discount = parseFloat(data.cart_discount) +
	      parseFloat(data.cart_discount_tax);

	      _.each( data.shipping_lines, function(item) {
	        item.total = parseFloat(item.total) + parseFloat(item.total_tax);
	      });

	      _.each( data.fee_lines, function(item) {
	        item.total = parseFloat(item.total) + parseFloat(item.total_tax);
	      });
	    }

	    data.has_tax = data.total_tax && 0 !== parseFloat(data.total_tax);
	    data.has_discount = data.cart_discount && 0 !== parseFloat(data.cart_discount);
	    data.has_order_discount = data.order_discount && 0 !== parseFloat(data.order_discount);

	    return data;
	  }

	});
	/* jshint  +W101, +W071, +W074 */

/***/ },
/* 206 */
/***/ function(module, exports, __webpack_require__) {

	var ReceiptView = __webpack_require__(205);
	var App = __webpack_require__(2);

	var View = ReceiptView.extend({
	  tagName: 'ul',
	  template: 'pos.receipt.totals'
	});

	module.exports = View;
	App.prototype.set('POSApp.Receipt.Views.Totals', View);

/***/ },
/* 207 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var App = __webpack_require__(2);
	var hbs = __webpack_require__(7);
	var polyglot = __webpack_require__(10);
	var Tmpl = __webpack_require__(208);

	var View = ItemView.extend({

	  template: hbs.compile(Tmpl),

	  viewOptions: ['email'],

	  initialize: function(options){
	    this.mergeOptions(options, this.viewOptions);
	    this.modal = {
	      header: {
	        title: polyglot.t('titles.email-receipt')
	      },
	      footer: {
	        buttons: [
	          {
	            type: 'message'
	          }, {
	            action: 'send',
	            className: 'btn-success',
	            icon: 'prepend'
	          }
	        ]
	      }
	    };
	  },

	  ui: {
	    email: 'input'
	  },

	  templateHelpers: function(){
	    var data = {
	      email: this.email
	    };
	    return data;
	  },

	  getEmail: function(){
	    return this.ui.email.val() || this.email;
	  }

	});

	module.exports = View;
	App.prototype.set('POSApp.Receipt.Views.Email', View);

/***/ },
/* 208 */
/***/ function(module, exports) {

	module.exports = "<div class=\"input-group\">\n  <span class=\"input-group-addon\">@</span>\n  <input type=\"text\" class=\"form-control\" placeholder=\"{{email}}\">\n</div>"

/***/ },
/* 209 */
/***/ function(module, exports, __webpack_require__) {

	var Router = __webpack_require__(81);
	var LayoutView = __webpack_require__(210);
	var FormRoute = __webpack_require__(211);
	var StatusRoute = __webpack_require__(214);
	var Radio = __webpack_require__(4);
	var Collection = __webpack_require__(24);
	var $ = __webpack_require__(6);

	module.exports = Router.extend({

	  routes: {
	    'support' : 'showStatus'
	  },

	  initialize: function(options) {
	    this.container = options.container;
	    this.collection = new Collection();
	  },

	  onBeforeEnter: function() {
	    this.layout = new LayoutView();
	    this.container.show(this.layout);
	    this.updateTitle();
	    this.showForm();
	  },

	  updateTitle: function(){
	    // TODO: put menu into params
	    var title = $('#menu li.support').text();
	    Radio.request('header', 'update:title', title);
	  },

	  showForm: function(){
	    var route = new FormRoute({
	      container  : this.layout.getRegion('left')
	    });
	    route.enter();
	  },

	  showStatus: function(){
	    return new StatusRoute({
	      container  : this.layout.getRegion('right'),
	      collection : this.collection
	    });
	  }

	});

/***/ },
/* 210 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);

	module.exports = LayoutView.extend({

	  columns: 2,

	  template: function(){
	    return '' +
	      '<section id="left"></section>' +
	      '<section id="right"></section>';
	  },

	  regions: {
	    left: '#left',
	    right: '#right'
	  }

	});

/***/ },
/* 211 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	var App = __webpack_require__(2);
	var Layout = __webpack_require__(212);
	var Form = __webpack_require__(213);
	var $ = __webpack_require__(6);
	var Radio = __webpack_require__(4);
	var Buttons = __webpack_require__(76);
	var _ = __webpack_require__(5);
	var polyglot = __webpack_require__(10);

	var FormRoute = Route.extend({

	  initialize: function(options){
	    this.container = options.container;
	    this.setTabLabel({
	      tab   : 'left',
	      label : polyglot.t('titles.support-form')
	    });
	  },

	  fetch: function(){

	  },

	  render: function(){
	    this.layout = new Layout();

	    this.listenTo(this.layout, 'show', function(){
	      this.showForm();
	      this.showActions();
	    });

	    this.container.show(this.layout);
	  },

	  showForm: function(){
	    var view = new Form();
	    this.layout.getRegion('form').show( view );
	  },

	  showActions: function(){
	    var view = new Buttons({
	      buttons: [{
	        type: 'message'
	      },{
	        action: 'send',
	        className: 'btn-success',
	        icon: 'prepend'
	      }]
	    });

	    this.listenTo(view, 'action:send', this.email);

	    this.layout.getRegion('actions').show(view);
	  },

	  email: function(btn, view){
	    var form = this.layout.getRegion('form').currentView,
	        data = {
	          action  : 'wc_pos_send_support_email',
	          name    : form.$('[data-name="name"]').text(),
	          email   : form.$('[data-name="email"]').text(),
	          message : form.$('[data-name="message"]').text(),
	          status  : form.$('#pos_status').val()
	        },
	        ajaxurl = Radio.request('entities', 'get', {
	          type: 'option',
	          name: 'ajaxurl'
	        });

	    btn.trigger('state', 'loading');
	    view.triggerMethod('message', 'reset');

	    var onError = function(message){
	      btn.trigger('state', 'error');
	      view.triggerMethod('message', message, 'error');
	    };

	    var onSuccess = function(data){
	      if(!_.isObject(data) || data.result !== 'success'){
	        return onError(data.message);
	      }
	      btn.trigger('state', 'success');
	      view.triggerMethod('message', data.message, 'success');
	    };

	    $.post( ajaxurl, data )
	    .done(onSuccess)
	    .fail(onError);
	  }

	});

	module.exports = FormRoute;
	App.prototype.set('SupportApp.Form.Route', FormRoute);


/***/ },
/* 212 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var App = __webpack_require__(2);
	var polyglot = __webpack_require__(10);

	var Layout = LayoutView.extend({
	  template: function(){
	    var title = polyglot.t('titles.support-form');
	    return '' +
	      '<div class="panel-header support-header">' +
	      '<div><h4>' + title + '</h4></div></div>' +
	      '<div class="panel-body list support-list"></div>' +
	      '<div class="list-actions support-actions"></div>';
	  },

	  regions: {
	    header   : '.support-header',
	    form     : '.support-list',
	    actions  : '.support-actions'
	  },

	  attributes: {
	    'class'  : 'panel support'
	  }

	});

	module.exports = Layout;
	App.prototype.set('SupportApp.Views.Layout', Layout);

/***/ },
/* 213 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var $ = __webpack_require__(6);

	module.exports = ItemView.extend({
	  tagName: 'ul',
	  template: 'support.form',

	  ui: {
	    toggle : '.toggle',
	    status : '#pos_status',
	    message: 'div[data-name="message"]'
	  },

	  events: {
	    'click @ui.toggle': 'toggleReport',
	    'focusout @ui.message': 'focusout'
	  },

	  onRender: function(){
	    this.ui.status.append('\n*** Browser Info ***\n\n' +
	      navigator.userAgent + '; ' + $('html').attr('class') + '\n\n');
	  },

	  toggleReport: function(e) {
	    e.preventDefault();
	    $(e.currentTarget).next('textarea').toggle();
	  },

	  focusout: function(e){
	    var element = $(e.target);
	    if (!element.text().replace(' ', '').length) {
	      element.empty();
	    }
	  }

	});

/***/ },
/* 214 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	var App = __webpack_require__(2);
	var Layout = __webpack_require__(215);
	var Status = __webpack_require__(216);
	var $ = __webpack_require__(6);
	var _ = __webpack_require__(5);
	//var Modernizr = global['Modernizr'];
	var Radio = __webpack_require__(4);
	var polyglot = __webpack_require__(10);
	//var debug = require('debug')('systemStatus');

	var StatusRoute = Route.extend({

	  databases: ['products', 'orders', 'cart', 'customers'],

	  initialize: function(options){
	    this.container = options.container;
	    this.collection = options.collection;
	    this.setTabLabel({
	      tab   : 'right',
	      label : polyglot.t('titles.system-status')
	    });

	    //this.ajaxurl = Radio.request('entities', 'get', {
	    //  type: 'option',
	    //  name: 'ajaxurl'
	    //});
	    //
	    //this.nonce = Radio.request('entities', 'get', {
	    //  type: 'option',
	    //  name: 'nonce'
	    //});
	  },

	  fetch: function(){
	    // if not fetched, need to fetch all local records
	    var fetched = _.map(this.databases, this.fetchDB, this);
	    // add the server tests
	    //fetched.push( this._fetch() );
	    return $.when.apply($, fetched);
	  },

	  _fetch: function(){
	    //var self = this;
	    //return $.getJSON( this.ajaxurl, {
	    //  action: 'wc_pos_system_status',
	    //  security: this.nonce
	    //}, function( resp ){
	    //  self.tests = resp;
	    //});
	  },

	  render: function(){
	    this.layout = new Layout();

	    this.listenTo(this.layout, 'show', function(){
	      this.showStatus();
	    });

	    this.container.show(this.layout);
	  },

	  showStatus: function(){
	    var view = new Status({
	      storage: this.storageStatus()
	    });

	    this.listenTo(view, {
	      'action:clear'  : this.clearDB
	    });

	    this.layout.getRegion('status').show( view );
	  },

	  storageStatus: function(){
	    return _.map(this.databases, function(db){
	      var title = polyglot.t('titles.' + db),
	          count = this[db].length,
	          message = title + ': ' + count + ' ' +
	            polyglot.t('plural.records', count);
	      return {
	        message : message,
	        button  : {
	          action: 'clear-' + db,
	          label : 'Clear'
	        }
	      };
	    }, this);
	  },

	  fetchDB: function(db){
	    this[db] = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: db
	    });

	    if(this[db].isNew()){
	      return this[db].fetch();
	    }
	  },

	  clearDB: function(db){
	    var collection = this[db],
	        self = this;

	    collection.clear()
	      .then(function(){
	        self.render();
	      });
	  }

	});

	module.exports = StatusRoute;
	App.prototype.set('SupportApp.Status.Route', StatusRoute);

/***/ },
/* 215 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);
	var App = __webpack_require__(2);
	var polyglot = __webpack_require__(10);

	var Layout = LayoutView.extend({
	  template: function(){
	    var title = polyglot.t('titles.system-status');
	    return '' +
	      '<div class="panel-header status-header">' +
	      '<div><h4>' + title + '</h4></div></div>' +
	      '<div class="panel-body list status-list"></div>';
	  },

	  regions: {
	    header   : '.status-header',
	    status   : '.status-list'
	  },

	  attributes: {
	    'class'  : 'panel status'
	  }

	});

	module.exports = Layout;
	App.prototype.set('SupportApp.Views.Layout', Layout);

/***/ },
/* 216 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(61);
	var $ = __webpack_require__(6);
	var EmulateHTTP = __webpack_require__(106);
	var Tmpl = __webpack_require__(217);
	var hbs = __webpack_require__(7);

	module.exports = ItemView.extend({
	  tagName: 'ul',
	  template: 'support.status',

	  ui: {
	    toggle      : '.toggle',
	    btn         : '.btn',
	    subHeading  : '.sub-heading'
	  },

	  events: {
	    'click @ui.toggle': 'toggleReport',
	    'click @ui.btn'   : 'buttonClick'
	  },

	  behaviors: {
	    EmulateHTTP: {
	      behaviorClass: EmulateHTTP
	    }
	  },

	  onRender: function(){
	    var storage = hbs.compile(Tmpl)({ storage: this.options.storage });
	    this.ui.subHeading.after(storage);
	  },

	  toggleReport: function(e){
	    e.preventDefault();
	    $(e.currentTarget).next('textarea').toggle();
	  },

	  buttonClick: function(e){
	    var action = $(e.target).data('action').split('-'),
	        key = action.shift();

	    if(key){
	      e.preventDefault();
	      this.trigger('action:' + key, action[0]);
	    }
	  }

	});

/***/ },
/* 217 */
/***/ function(module, exports) {

	module.exports = "{{#each storage}}\n<li class=\"list-row\">\n  {{#if icon}}\n  <div class=\"shrink\">\n    <i class=\"icon-{{icon}} icon-lg\"></i>\n  </div>\n  {{/if}}\n  {{#if title}}\n  <div class=\"title\">{{title}}</div>\n  {{/if}}\n  <div class=\"message\">\n    {{{message}}}\n    {{#if button}}\n      <button class=\"btn btn-default\" data-action=\"{{button.action}}\">{{button.label}}</button>\n    {{/if}}\n  </div>\n</li>\n{{/each}}"

/***/ },
/* 218 */
/***/ function(module, exports, __webpack_require__) {

	var App = __webpack_require__(2);
	var Router = __webpack_require__(81);
	var LayoutView = __webpack_require__(219);
	var ReceiptRoute = __webpack_require__(220);
	var Radio = __webpack_require__(4);

	var PrintRouter = Router.extend({
	  routes: {
	    'print(/:id)(/)' : 'showReceipt'
	  },

	  initialize: function(options) {
	    this.container = options.container;
	  },

	  onBeforeEnter: function() {
	    this.layout = new LayoutView();
	    this.container.show(this.layout);
	  },

	  onBeforeRoute: function(){
	    this.showActions();
	  },

	  showReceipt: function(){
	    return new ReceiptRoute({
	      container: this.layout.getRegion('iframe')
	    });
	  },

	  showActions: function(){
	    var buttons = Radio.request('buttons', 'view', {
	      buttons: [{
	        action: 'print',
	        className: 'btn-success'
	      }]
	    });

	    this.listenTo(buttons, 'action:print', function(){
	      this.layout.getRegion('iframe').currentView.print();
	    });

	    this.layout.getRegion('actions').show(buttons);
	  }

	});

	module.exports = PrintRouter;
	App.prototype.set('Print.Router', PrintRouter);

/***/ },
/* 219 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(18);

	module.exports = LayoutView.extend({

	  tagName: 'section',

	  className: 'panel print-preview',

	  template: function(){
	    return '' +
	      '<div class="panel-body print-preview-iframe"></div>' +
	      '<div class="print-preview-actions"></div>';
	  },

	  regions: {
	    iframe  : '.print-preview-iframe',
	    actions : '.print-preview-actions'
	  }

	});

/***/ },
/* 220 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(82);
	var Radio = __webpack_require__(4);
	var View = __webpack_require__(221);

	var ReceiptRoute = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.collection = Radio.request('entities', 'get', {
	      name: 'orders',
	      type: 'collection'
	    });
	  },

	  fetch: function() {
	    if (this.collection.length === 0) {
	      return this.collection.fetch({ remote: true });
	    }
	  },

	  render: function() {
	    var view = new View({
	      model: this.collection.at(0)
	    });
	    this.container.show(view);
	  }

	});

	module.exports = ReceiptRoute;

/***/ },
/* 221 */
/***/ function(module, exports, __webpack_require__) {

	var ReceiptView = __webpack_require__(205);
	var hbs = __webpack_require__(7);

	module.exports = ReceiptView.extend({

	  tagName: 'iframe',

	  template: function(){},

	  onShow: function(){
	    var template = hbs.compile( hbs.Templates.print.receipt ) ;
	    this.window = this.el.contentWindow;
	    this.window.document.write(template( this.data ));
	  },

	  print: function(){
	    this.window.focus(); // required for IE
	    this.window.print();
	  }

	});

/***/ }
/******/ ]);