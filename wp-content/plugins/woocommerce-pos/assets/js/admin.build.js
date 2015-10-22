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

	__webpack_require__(1);
	var POS = __webpack_require__(36);
	var Application = __webpack_require__(42);

	/**
	 * Services
	 */
	var EntitiesService = __webpack_require__(52);
	var ModalService = __webpack_require__(92);
	var TabsService = __webpack_require__(103);
	var ButtonsService = __webpack_require__(110);

	/**
	 * SubApps
	 */
	var SettingsRouter = __webpack_require__(115);

	/**
	 * Create the app
	 */
	var app = new Application();

	/**
	 * ... add SubApps and Services
	 */
	app.entities = new EntitiesService({
	  app: app
	});

	app.settingsApp = new SettingsRouter({
	  container: app.layout.getRegion('main')
	});

	app.modalApp = new ModalService({
	  container: app.layout.getRegion('modal')
	});

	app.tabsService = new TabsService();
	app.buttonsService = new ButtonsService();

	/**
	 * Attach app to window for third party plugins
	 */
	module.exports = window.POS = POS.create(app);

/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	// mixins for WP Admin underscore
	var _ = __webpack_require__(2);

	_.mixin({
	  merge : __webpack_require__(3)
	});

/***/ },
/* 2 */
/***/ function(module, exports) {

	module.exports = _;

/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	var baseMerge = __webpack_require__(4),
	    createAssigner = __webpack_require__(31);

	/**
	 * Recursively merges own enumerable properties of the source object(s), that
	 * don't resolve to `undefined` into the destination object. Subsequent sources
	 * overwrite property assignments of previous sources. If `customizer` is
	 * provided it's invoked to produce the merged values of the destination and
	 * source properties. If `customizer` returns `undefined` merging is handled
	 * by the method instead. The `customizer` is bound to `thisArg` and invoked
	 * with five arguments: (objectValue, sourceValue, key, object, source).
	 *
	 * @static
	 * @memberOf _
	 * @category Object
	 * @param {Object} object The destination object.
	 * @param {...Object} [sources] The source objects.
	 * @param {Function} [customizer] The function to customize assigned values.
	 * @param {*} [thisArg] The `this` binding of `customizer`.
	 * @returns {Object} Returns `object`.
	 * @example
	 *
	 * var users = {
	 *   'data': [{ 'user': 'barney' }, { 'user': 'fred' }]
	 * };
	 *
	 * var ages = {
	 *   'data': [{ 'age': 36 }, { 'age': 40 }]
	 * };
	 *
	 * _.merge(users, ages);
	 * // => { 'data': [{ 'user': 'barney', 'age': 36 }, { 'user': 'fred', 'age': 40 }] }
	 *
	 * // using a customizer callback
	 * var object = {
	 *   'fruits': ['apple'],
	 *   'vegetables': ['beet']
	 * };
	 *
	 * var other = {
	 *   'fruits': ['banana'],
	 *   'vegetables': ['carrot']
	 * };
	 *
	 * _.merge(object, other, function(a, b) {
	 *   if (_.isArray(a)) {
	 *     return a.concat(b);
	 *   }
	 * });
	 * // => { 'fruits': ['apple', 'banana'], 'vegetables': ['beet', 'carrot'] }
	 */
	var merge = createAssigner(baseMerge);

	module.exports = merge;


/***/ },
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	var arrayEach = __webpack_require__(5),
	    baseMergeDeep = __webpack_require__(6),
	    isArray = __webpack_require__(14),
	    isArrayLike = __webpack_require__(9),
	    isObject = __webpack_require__(18),
	    isObjectLike = __webpack_require__(13),
	    isTypedArray = __webpack_require__(26),
	    keys = __webpack_require__(29);

	/**
	 * The base implementation of `_.merge` without support for argument juggling,
	 * multiple sources, and `this` binding `customizer` functions.
	 *
	 * @private
	 * @param {Object} object The destination object.
	 * @param {Object} source The source object.
	 * @param {Function} [customizer] The function to customize merged values.
	 * @param {Array} [stackA=[]] Tracks traversed source objects.
	 * @param {Array} [stackB=[]] Associates values with source counterparts.
	 * @returns {Object} Returns `object`.
	 */
	function baseMerge(object, source, customizer, stackA, stackB) {
	  if (!isObject(object)) {
	    return object;
	  }
	  var isSrcArr = isArrayLike(source) && (isArray(source) || isTypedArray(source)),
	      props = isSrcArr ? undefined : keys(source);

	  arrayEach(props || source, function(srcValue, key) {
	    if (props) {
	      key = srcValue;
	      srcValue = source[key];
	    }
	    if (isObjectLike(srcValue)) {
	      stackA || (stackA = []);
	      stackB || (stackB = []);
	      baseMergeDeep(object, source, key, baseMerge, customizer, stackA, stackB);
	    }
	    else {
	      var value = object[key],
	          result = customizer ? customizer(value, srcValue, key, object, source) : undefined,
	          isCommon = result === undefined;

	      if (isCommon) {
	        result = srcValue;
	      }
	      if ((result !== undefined || (isSrcArr && !(key in object))) &&
	          (isCommon || (result === result ? (result !== value) : (value === value)))) {
	        object[key] = result;
	      }
	    }
	  });
	  return object;
	}

	module.exports = baseMerge;


/***/ },
/* 5 */
/***/ function(module, exports) {

	/**
	 * A specialized version of `_.forEach` for arrays without support for callback
	 * shorthands and `this` binding.
	 *
	 * @private
	 * @param {Array} array The array to iterate over.
	 * @param {Function} iteratee The function invoked per iteration.
	 * @returns {Array} Returns `array`.
	 */
	function arrayEach(array, iteratee) {
	  var index = -1,
	      length = array.length;

	  while (++index < length) {
	    if (iteratee(array[index], index, array) === false) {
	      break;
	    }
	  }
	  return array;
	}

	module.exports = arrayEach;


/***/ },
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	var arrayCopy = __webpack_require__(7),
	    isArguments = __webpack_require__(8),
	    isArray = __webpack_require__(14),
	    isArrayLike = __webpack_require__(9),
	    isPlainObject = __webpack_require__(19),
	    isTypedArray = __webpack_require__(26),
	    toPlainObject = __webpack_require__(27);

	/**
	 * A specialized version of `baseMerge` for arrays and objects which performs
	 * deep merges and tracks traversed objects enabling objects with circular
	 * references to be merged.
	 *
	 * @private
	 * @param {Object} object The destination object.
	 * @param {Object} source The source object.
	 * @param {string} key The key of the value to merge.
	 * @param {Function} mergeFunc The function to merge values.
	 * @param {Function} [customizer] The function to customize merged values.
	 * @param {Array} [stackA=[]] Tracks traversed source objects.
	 * @param {Array} [stackB=[]] Associates values with source counterparts.
	 * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
	 */
	function baseMergeDeep(object, source, key, mergeFunc, customizer, stackA, stackB) {
	  var length = stackA.length,
	      srcValue = source[key];

	  while (length--) {
	    if (stackA[length] == srcValue) {
	      object[key] = stackB[length];
	      return;
	    }
	  }
	  var value = object[key],
	      result = customizer ? customizer(value, srcValue, key, object, source) : undefined,
	      isCommon = result === undefined;

	  if (isCommon) {
	    result = srcValue;
	    if (isArrayLike(srcValue) && (isArray(srcValue) || isTypedArray(srcValue))) {
	      result = isArray(value)
	        ? value
	        : (isArrayLike(value) ? arrayCopy(value) : []);
	    }
	    else if (isPlainObject(srcValue) || isArguments(srcValue)) {
	      result = isArguments(value)
	        ? toPlainObject(value)
	        : (isPlainObject(value) ? value : {});
	    }
	    else {
	      isCommon = false;
	    }
	  }
	  // Add the source value to the stack of traversed objects and associate
	  // it with its merged value.
	  stackA.push(srcValue);
	  stackB.push(result);

	  if (isCommon) {
	    // Recursively merge objects and arrays (susceptible to call stack limits).
	    object[key] = mergeFunc(result, srcValue, customizer, stackA, stackB);
	  } else if (result === result ? (result !== value) : (value === value)) {
	    object[key] = result;
	  }
	}

	module.exports = baseMergeDeep;


/***/ },
/* 7 */
/***/ function(module, exports) {

	/**
	 * Copies the values of `source` to `array`.
	 *
	 * @private
	 * @param {Array} source The array to copy values from.
	 * @param {Array} [array=[]] The array to copy values to.
	 * @returns {Array} Returns `array`.
	 */
	function arrayCopy(source, array) {
	  var index = -1,
	      length = source.length;

	  array || (array = Array(length));
	  while (++index < length) {
	    array[index] = source[index];
	  }
	  return array;
	}

	module.exports = arrayCopy;


/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	var isArrayLike = __webpack_require__(9),
	    isObjectLike = __webpack_require__(13);

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/** Used to check objects for own properties. */
	var hasOwnProperty = objectProto.hasOwnProperty;

	/** Native method references. */
	var propertyIsEnumerable = objectProto.propertyIsEnumerable;

	/**
	 * Checks if `value` is classified as an `arguments` object.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
	 * @example
	 *
	 * _.isArguments(function() { return arguments; }());
	 * // => true
	 *
	 * _.isArguments([1, 2, 3]);
	 * // => false
	 */
	function isArguments(value) {
	  return isObjectLike(value) && isArrayLike(value) &&
	    hasOwnProperty.call(value, 'callee') && !propertyIsEnumerable.call(value, 'callee');
	}

	module.exports = isArguments;


/***/ },
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var getLength = __webpack_require__(10),
	    isLength = __webpack_require__(12);

	/**
	 * Checks if `value` is array-like.
	 *
	 * @private
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
	 */
	function isArrayLike(value) {
	  return value != null && isLength(getLength(value));
	}

	module.exports = isArrayLike;


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var baseProperty = __webpack_require__(11);

	/**
	 * Gets the "length" property value of `object`.
	 *
	 * **Note:** This function is used to avoid a [JIT bug](https://bugs.webkit.org/show_bug.cgi?id=142792)
	 * that affects Safari on at least iOS 8.1-8.3 ARM64.
	 *
	 * @private
	 * @param {Object} object The object to query.
	 * @returns {*} Returns the "length" value.
	 */
	var getLength = baseProperty('length');

	module.exports = getLength;


/***/ },
/* 11 */
/***/ function(module, exports) {

	/**
	 * The base implementation of `_.property` without support for deep paths.
	 *
	 * @private
	 * @param {string} key The key of the property to get.
	 * @returns {Function} Returns the new function.
	 */
	function baseProperty(key) {
	  return function(object) {
	    return object == null ? undefined : object[key];
	  };
	}

	module.exports = baseProperty;


/***/ },
/* 12 */
/***/ function(module, exports) {

	/**
	 * Used as the [maximum length](http://ecma-international.org/ecma-262/6.0/#sec-number.max_safe_integer)
	 * of an array-like value.
	 */
	var MAX_SAFE_INTEGER = 9007199254740991;

	/**
	 * Checks if `value` is a valid array-like length.
	 *
	 * **Note:** This function is based on [`ToLength`](http://ecma-international.org/ecma-262/6.0/#sec-tolength).
	 *
	 * @private
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
	 */
	function isLength(value) {
	  return typeof value == 'number' && value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
	}

	module.exports = isLength;


/***/ },
/* 13 */
/***/ function(module, exports) {

	/**
	 * Checks if `value` is object-like.
	 *
	 * @private
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
	 */
	function isObjectLike(value) {
	  return !!value && typeof value == 'object';
	}

	module.exports = isObjectLike;


/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	var getNative = __webpack_require__(15),
	    isLength = __webpack_require__(12),
	    isObjectLike = __webpack_require__(13);

	/** `Object#toString` result references. */
	var arrayTag = '[object Array]';

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/**
	 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
	 * of values.
	 */
	var objToString = objectProto.toString;

	/* Native method references for those with the same name as other `lodash` methods. */
	var nativeIsArray = getNative(Array, 'isArray');

	/**
	 * Checks if `value` is classified as an `Array` object.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
	 * @example
	 *
	 * _.isArray([1, 2, 3]);
	 * // => true
	 *
	 * _.isArray(function() { return arguments; }());
	 * // => false
	 */
	var isArray = nativeIsArray || function(value) {
	  return isObjectLike(value) && isLength(value.length) && objToString.call(value) == arrayTag;
	};

	module.exports = isArray;


/***/ },
/* 15 */
/***/ function(module, exports, __webpack_require__) {

	var isNative = __webpack_require__(16);

	/**
	 * Gets the native function at `key` of `object`.
	 *
	 * @private
	 * @param {Object} object The object to query.
	 * @param {string} key The key of the method to get.
	 * @returns {*} Returns the function if it's native, else `undefined`.
	 */
	function getNative(object, key) {
	  var value = object == null ? undefined : object[key];
	  return isNative(value) ? value : undefined;
	}

	module.exports = getNative;


/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	var isFunction = __webpack_require__(17),
	    isObjectLike = __webpack_require__(13);

	/** Used to detect host constructors (Safari > 5). */
	var reIsHostCtor = /^\[object .+?Constructor\]$/;

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/** Used to resolve the decompiled source of functions. */
	var fnToString = Function.prototype.toString;

	/** Used to check objects for own properties. */
	var hasOwnProperty = objectProto.hasOwnProperty;

	/** Used to detect if a method is native. */
	var reIsNative = RegExp('^' +
	  fnToString.call(hasOwnProperty).replace(/[\\^$.*+?()[\]{}|]/g, '\\$&')
	  .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
	);

	/**
	 * Checks if `value` is a native function.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is a native function, else `false`.
	 * @example
	 *
	 * _.isNative(Array.prototype.push);
	 * // => true
	 *
	 * _.isNative(_);
	 * // => false
	 */
	function isNative(value) {
	  if (value == null) {
	    return false;
	  }
	  if (isFunction(value)) {
	    return reIsNative.test(fnToString.call(value));
	  }
	  return isObjectLike(value) && reIsHostCtor.test(value);
	}

	module.exports = isNative;


/***/ },
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	var isObject = __webpack_require__(18);

	/** `Object#toString` result references. */
	var funcTag = '[object Function]';

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/**
	 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
	 * of values.
	 */
	var objToString = objectProto.toString;

	/**
	 * Checks if `value` is classified as a `Function` object.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
	 * @example
	 *
	 * _.isFunction(_);
	 * // => true
	 *
	 * _.isFunction(/abc/);
	 * // => false
	 */
	function isFunction(value) {
	  // The use of `Object#toString` avoids issues with the `typeof` operator
	  // in older versions of Chrome and Safari which return 'function' for regexes
	  // and Safari 8 which returns 'object' for typed array constructors.
	  return isObject(value) && objToString.call(value) == funcTag;
	}

	module.exports = isFunction;


/***/ },
/* 18 */
/***/ function(module, exports) {

	/**
	 * Checks if `value` is the [language type](https://es5.github.io/#x8) of `Object`.
	 * (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is an object, else `false`.
	 * @example
	 *
	 * _.isObject({});
	 * // => true
	 *
	 * _.isObject([1, 2, 3]);
	 * // => true
	 *
	 * _.isObject(1);
	 * // => false
	 */
	function isObject(value) {
	  // Avoid a V8 JIT bug in Chrome 19-20.
	  // See https://code.google.com/p/v8/issues/detail?id=2291 for more details.
	  var type = typeof value;
	  return !!value && (type == 'object' || type == 'function');
	}

	module.exports = isObject;


/***/ },
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	var baseForIn = __webpack_require__(20),
	    isArguments = __webpack_require__(8),
	    isObjectLike = __webpack_require__(13);

	/** `Object#toString` result references. */
	var objectTag = '[object Object]';

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/** Used to check objects for own properties. */
	var hasOwnProperty = objectProto.hasOwnProperty;

	/**
	 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
	 * of values.
	 */
	var objToString = objectProto.toString;

	/**
	 * Checks if `value` is a plain object, that is, an object created by the
	 * `Object` constructor or one with a `[[Prototype]]` of `null`.
	 *
	 * **Note:** This method assumes objects created by the `Object` constructor
	 * have no inherited enumerable properties.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is a plain object, else `false`.
	 * @example
	 *
	 * function Foo() {
	 *   this.a = 1;
	 * }
	 *
	 * _.isPlainObject(new Foo);
	 * // => false
	 *
	 * _.isPlainObject([1, 2, 3]);
	 * // => false
	 *
	 * _.isPlainObject({ 'x': 0, 'y': 0 });
	 * // => true
	 *
	 * _.isPlainObject(Object.create(null));
	 * // => true
	 */
	function isPlainObject(value) {
	  var Ctor;

	  // Exit early for non `Object` objects.
	  if (!(isObjectLike(value) && objToString.call(value) == objectTag && !isArguments(value)) ||
	      (!hasOwnProperty.call(value, 'constructor') && (Ctor = value.constructor, typeof Ctor == 'function' && !(Ctor instanceof Ctor)))) {
	    return false;
	  }
	  // IE < 9 iterates inherited properties before own properties. If the first
	  // iterated property is an object's own property then there are no inherited
	  // enumerable properties.
	  var result;
	  // In most environments an object's own properties are iterated before
	  // its inherited properties. If the last iterated property is an object's
	  // own property then there are no inherited enumerable properties.
	  baseForIn(value, function(subValue, key) {
	    result = key;
	  });
	  return result === undefined || hasOwnProperty.call(value, result);
	}

	module.exports = isPlainObject;


/***/ },
/* 20 */
/***/ function(module, exports, __webpack_require__) {

	var baseFor = __webpack_require__(21),
	    keysIn = __webpack_require__(24);

	/**
	 * The base implementation of `_.forIn` without support for callback
	 * shorthands and `this` binding.
	 *
	 * @private
	 * @param {Object} object The object to iterate over.
	 * @param {Function} iteratee The function invoked per iteration.
	 * @returns {Object} Returns `object`.
	 */
	function baseForIn(object, iteratee) {
	  return baseFor(object, iteratee, keysIn);
	}

	module.exports = baseForIn;


/***/ },
/* 21 */
/***/ function(module, exports, __webpack_require__) {

	var createBaseFor = __webpack_require__(22);

	/**
	 * The base implementation of `baseForIn` and `baseForOwn` which iterates
	 * over `object` properties returned by `keysFunc` invoking `iteratee` for
	 * each property. Iteratee functions may exit iteration early by explicitly
	 * returning `false`.
	 *
	 * @private
	 * @param {Object} object The object to iterate over.
	 * @param {Function} iteratee The function invoked per iteration.
	 * @param {Function} keysFunc The function to get the keys of `object`.
	 * @returns {Object} Returns `object`.
	 */
	var baseFor = createBaseFor();

	module.exports = baseFor;


/***/ },
/* 22 */
/***/ function(module, exports, __webpack_require__) {

	var toObject = __webpack_require__(23);

	/**
	 * Creates a base function for `_.forIn` or `_.forInRight`.
	 *
	 * @private
	 * @param {boolean} [fromRight] Specify iterating from right to left.
	 * @returns {Function} Returns the new base function.
	 */
	function createBaseFor(fromRight) {
	  return function(object, iteratee, keysFunc) {
	    var iterable = toObject(object),
	        props = keysFunc(object),
	        length = props.length,
	        index = fromRight ? length : -1;

	    while ((fromRight ? index-- : ++index < length)) {
	      var key = props[index];
	      if (iteratee(iterable[key], key, iterable) === false) {
	        break;
	      }
	    }
	    return object;
	  };
	}

	module.exports = createBaseFor;


/***/ },
/* 23 */
/***/ function(module, exports, __webpack_require__) {

	var isObject = __webpack_require__(18);

	/**
	 * Converts `value` to an object if it's not one.
	 *
	 * @private
	 * @param {*} value The value to process.
	 * @returns {Object} Returns the object.
	 */
	function toObject(value) {
	  return isObject(value) ? value : Object(value);
	}

	module.exports = toObject;


/***/ },
/* 24 */
/***/ function(module, exports, __webpack_require__) {

	var isArguments = __webpack_require__(8),
	    isArray = __webpack_require__(14),
	    isIndex = __webpack_require__(25),
	    isLength = __webpack_require__(12),
	    isObject = __webpack_require__(18);

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/** Used to check objects for own properties. */
	var hasOwnProperty = objectProto.hasOwnProperty;

	/**
	 * Creates an array of the own and inherited enumerable property names of `object`.
	 *
	 * **Note:** Non-object values are coerced to objects.
	 *
	 * @static
	 * @memberOf _
	 * @category Object
	 * @param {Object} object The object to query.
	 * @returns {Array} Returns the array of property names.
	 * @example
	 *
	 * function Foo() {
	 *   this.a = 1;
	 *   this.b = 2;
	 * }
	 *
	 * Foo.prototype.c = 3;
	 *
	 * _.keysIn(new Foo);
	 * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
	 */
	function keysIn(object) {
	  if (object == null) {
	    return [];
	  }
	  if (!isObject(object)) {
	    object = Object(object);
	  }
	  var length = object.length;
	  length = (length && isLength(length) &&
	    (isArray(object) || isArguments(object)) && length) || 0;

	  var Ctor = object.constructor,
	      index = -1,
	      isProto = typeof Ctor == 'function' && Ctor.prototype === object,
	      result = Array(length),
	      skipIndexes = length > 0;

	  while (++index < length) {
	    result[index] = (index + '');
	  }
	  for (var key in object) {
	    if (!(skipIndexes && isIndex(key, length)) &&
	        !(key == 'constructor' && (isProto || !hasOwnProperty.call(object, key)))) {
	      result.push(key);
	    }
	  }
	  return result;
	}

	module.exports = keysIn;


/***/ },
/* 25 */
/***/ function(module, exports) {

	/** Used to detect unsigned integer values. */
	var reIsUint = /^\d+$/;

	/**
	 * Used as the [maximum length](http://ecma-international.org/ecma-262/6.0/#sec-number.max_safe_integer)
	 * of an array-like value.
	 */
	var MAX_SAFE_INTEGER = 9007199254740991;

	/**
	 * Checks if `value` is a valid array-like index.
	 *
	 * @private
	 * @param {*} value The value to check.
	 * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
	 * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
	 */
	function isIndex(value, length) {
	  value = (typeof value == 'number' || reIsUint.test(value)) ? +value : -1;
	  length = length == null ? MAX_SAFE_INTEGER : length;
	  return value > -1 && value % 1 == 0 && value < length;
	}

	module.exports = isIndex;


/***/ },
/* 26 */
/***/ function(module, exports, __webpack_require__) {

	var isLength = __webpack_require__(12),
	    isObjectLike = __webpack_require__(13);

	/** `Object#toString` result references. */
	var argsTag = '[object Arguments]',
	    arrayTag = '[object Array]',
	    boolTag = '[object Boolean]',
	    dateTag = '[object Date]',
	    errorTag = '[object Error]',
	    funcTag = '[object Function]',
	    mapTag = '[object Map]',
	    numberTag = '[object Number]',
	    objectTag = '[object Object]',
	    regexpTag = '[object RegExp]',
	    setTag = '[object Set]',
	    stringTag = '[object String]',
	    weakMapTag = '[object WeakMap]';

	var arrayBufferTag = '[object ArrayBuffer]',
	    float32Tag = '[object Float32Array]',
	    float64Tag = '[object Float64Array]',
	    int8Tag = '[object Int8Array]',
	    int16Tag = '[object Int16Array]',
	    int32Tag = '[object Int32Array]',
	    uint8Tag = '[object Uint8Array]',
	    uint8ClampedTag = '[object Uint8ClampedArray]',
	    uint16Tag = '[object Uint16Array]',
	    uint32Tag = '[object Uint32Array]';

	/** Used to identify `toStringTag` values of typed arrays. */
	var typedArrayTags = {};
	typedArrayTags[float32Tag] = typedArrayTags[float64Tag] =
	typedArrayTags[int8Tag] = typedArrayTags[int16Tag] =
	typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] =
	typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] =
	typedArrayTags[uint32Tag] = true;
	typedArrayTags[argsTag] = typedArrayTags[arrayTag] =
	typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] =
	typedArrayTags[dateTag] = typedArrayTags[errorTag] =
	typedArrayTags[funcTag] = typedArrayTags[mapTag] =
	typedArrayTags[numberTag] = typedArrayTags[objectTag] =
	typedArrayTags[regexpTag] = typedArrayTags[setTag] =
	typedArrayTags[stringTag] = typedArrayTags[weakMapTag] = false;

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/**
	 * Used to resolve the [`toStringTag`](http://ecma-international.org/ecma-262/6.0/#sec-object.prototype.tostring)
	 * of values.
	 */
	var objToString = objectProto.toString;

	/**
	 * Checks if `value` is classified as a typed array.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to check.
	 * @returns {boolean} Returns `true` if `value` is correctly classified, else `false`.
	 * @example
	 *
	 * _.isTypedArray(new Uint8Array);
	 * // => true
	 *
	 * _.isTypedArray([]);
	 * // => false
	 */
	function isTypedArray(value) {
	  return isObjectLike(value) && isLength(value.length) && !!typedArrayTags[objToString.call(value)];
	}

	module.exports = isTypedArray;


/***/ },
/* 27 */
/***/ function(module, exports, __webpack_require__) {

	var baseCopy = __webpack_require__(28),
	    keysIn = __webpack_require__(24);

	/**
	 * Converts `value` to a plain object flattening inherited enumerable
	 * properties of `value` to own properties of the plain object.
	 *
	 * @static
	 * @memberOf _
	 * @category Lang
	 * @param {*} value The value to convert.
	 * @returns {Object} Returns the converted plain object.
	 * @example
	 *
	 * function Foo() {
	 *   this.b = 2;
	 * }
	 *
	 * Foo.prototype.c = 3;
	 *
	 * _.assign({ 'a': 1 }, new Foo);
	 * // => { 'a': 1, 'b': 2 }
	 *
	 * _.assign({ 'a': 1 }, _.toPlainObject(new Foo));
	 * // => { 'a': 1, 'b': 2, 'c': 3 }
	 */
	function toPlainObject(value) {
	  return baseCopy(value, keysIn(value));
	}

	module.exports = toPlainObject;


/***/ },
/* 28 */
/***/ function(module, exports) {

	/**
	 * Copies properties of `source` to `object`.
	 *
	 * @private
	 * @param {Object} source The object to copy properties from.
	 * @param {Array} props The property names to copy.
	 * @param {Object} [object={}] The object to copy properties to.
	 * @returns {Object} Returns `object`.
	 */
	function baseCopy(source, props, object) {
	  object || (object = {});

	  var index = -1,
	      length = props.length;

	  while (++index < length) {
	    var key = props[index];
	    object[key] = source[key];
	  }
	  return object;
	}

	module.exports = baseCopy;


/***/ },
/* 29 */
/***/ function(module, exports, __webpack_require__) {

	var getNative = __webpack_require__(15),
	    isArrayLike = __webpack_require__(9),
	    isObject = __webpack_require__(18),
	    shimKeys = __webpack_require__(30);

	/* Native method references for those with the same name as other `lodash` methods. */
	var nativeKeys = getNative(Object, 'keys');

	/**
	 * Creates an array of the own enumerable property names of `object`.
	 *
	 * **Note:** Non-object values are coerced to objects. See the
	 * [ES spec](http://ecma-international.org/ecma-262/6.0/#sec-object.keys)
	 * for more details.
	 *
	 * @static
	 * @memberOf _
	 * @category Object
	 * @param {Object} object The object to query.
	 * @returns {Array} Returns the array of property names.
	 * @example
	 *
	 * function Foo() {
	 *   this.a = 1;
	 *   this.b = 2;
	 * }
	 *
	 * Foo.prototype.c = 3;
	 *
	 * _.keys(new Foo);
	 * // => ['a', 'b'] (iteration order is not guaranteed)
	 *
	 * _.keys('hi');
	 * // => ['0', '1']
	 */
	var keys = !nativeKeys ? shimKeys : function(object) {
	  var Ctor = object == null ? undefined : object.constructor;
	  if ((typeof Ctor == 'function' && Ctor.prototype === object) ||
	      (typeof object != 'function' && isArrayLike(object))) {
	    return shimKeys(object);
	  }
	  return isObject(object) ? nativeKeys(object) : [];
	};

	module.exports = keys;


/***/ },
/* 30 */
/***/ function(module, exports, __webpack_require__) {

	var isArguments = __webpack_require__(8),
	    isArray = __webpack_require__(14),
	    isIndex = __webpack_require__(25),
	    isLength = __webpack_require__(12),
	    keysIn = __webpack_require__(24);

	/** Used for native method references. */
	var objectProto = Object.prototype;

	/** Used to check objects for own properties. */
	var hasOwnProperty = objectProto.hasOwnProperty;

	/**
	 * A fallback implementation of `Object.keys` which creates an array of the
	 * own enumerable property names of `object`.
	 *
	 * @private
	 * @param {Object} object The object to query.
	 * @returns {Array} Returns the array of property names.
	 */
	function shimKeys(object) {
	  var props = keysIn(object),
	      propsLength = props.length,
	      length = propsLength && object.length;

	  var allowIndexes = !!length && isLength(length) &&
	    (isArray(object) || isArguments(object));

	  var index = -1,
	      result = [];

	  while (++index < propsLength) {
	    var key = props[index];
	    if ((allowIndexes && isIndex(key, length)) || hasOwnProperty.call(object, key)) {
	      result.push(key);
	    }
	  }
	  return result;
	}

	module.exports = shimKeys;


/***/ },
/* 31 */
/***/ function(module, exports, __webpack_require__) {

	var bindCallback = __webpack_require__(32),
	    isIterateeCall = __webpack_require__(34),
	    restParam = __webpack_require__(35);

	/**
	 * Creates a `_.assign`, `_.defaults`, or `_.merge` function.
	 *
	 * @private
	 * @param {Function} assigner The function to assign values.
	 * @returns {Function} Returns the new assigner function.
	 */
	function createAssigner(assigner) {
	  return restParam(function(object, sources) {
	    var index = -1,
	        length = object == null ? 0 : sources.length,
	        customizer = length > 2 ? sources[length - 2] : undefined,
	        guard = length > 2 ? sources[2] : undefined,
	        thisArg = length > 1 ? sources[length - 1] : undefined;

	    if (typeof customizer == 'function') {
	      customizer = bindCallback(customizer, thisArg, 5);
	      length -= 2;
	    } else {
	      customizer = typeof thisArg == 'function' ? thisArg : undefined;
	      length -= (customizer ? 1 : 0);
	    }
	    if (guard && isIterateeCall(sources[0], sources[1], guard)) {
	      customizer = length < 3 ? undefined : customizer;
	      length = 1;
	    }
	    while (++index < length) {
	      var source = sources[index];
	      if (source) {
	        assigner(object, source, customizer);
	      }
	    }
	    return object;
	  });
	}

	module.exports = createAssigner;


/***/ },
/* 32 */
/***/ function(module, exports, __webpack_require__) {

	var identity = __webpack_require__(33);

	/**
	 * A specialized version of `baseCallback` which only supports `this` binding
	 * and specifying the number of arguments to provide to `func`.
	 *
	 * @private
	 * @param {Function} func The function to bind.
	 * @param {*} thisArg The `this` binding of `func`.
	 * @param {number} [argCount] The number of arguments to provide to `func`.
	 * @returns {Function} Returns the callback.
	 */
	function bindCallback(func, thisArg, argCount) {
	  if (typeof func != 'function') {
	    return identity;
	  }
	  if (thisArg === undefined) {
	    return func;
	  }
	  switch (argCount) {
	    case 1: return function(value) {
	      return func.call(thisArg, value);
	    };
	    case 3: return function(value, index, collection) {
	      return func.call(thisArg, value, index, collection);
	    };
	    case 4: return function(accumulator, value, index, collection) {
	      return func.call(thisArg, accumulator, value, index, collection);
	    };
	    case 5: return function(value, other, key, object, source) {
	      return func.call(thisArg, value, other, key, object, source);
	    };
	  }
	  return function() {
	    return func.apply(thisArg, arguments);
	  };
	}

	module.exports = bindCallback;


/***/ },
/* 33 */
/***/ function(module, exports) {

	/**
	 * This method returns the first argument provided to it.
	 *
	 * @static
	 * @memberOf _
	 * @category Utility
	 * @param {*} value Any value.
	 * @returns {*} Returns `value`.
	 * @example
	 *
	 * var object = { 'user': 'fred' };
	 *
	 * _.identity(object) === object;
	 * // => true
	 */
	function identity(value) {
	  return value;
	}

	module.exports = identity;


/***/ },
/* 34 */
/***/ function(module, exports, __webpack_require__) {

	var isArrayLike = __webpack_require__(9),
	    isIndex = __webpack_require__(25),
	    isObject = __webpack_require__(18);

	/**
	 * Checks if the provided arguments are from an iteratee call.
	 *
	 * @private
	 * @param {*} value The potential iteratee value argument.
	 * @param {*} index The potential iteratee index or key argument.
	 * @param {*} object The potential iteratee object argument.
	 * @returns {boolean} Returns `true` if the arguments are from an iteratee call, else `false`.
	 */
	function isIterateeCall(value, index, object) {
	  if (!isObject(object)) {
	    return false;
	  }
	  var type = typeof index;
	  if (type == 'number'
	      ? (isArrayLike(object) && isIndex(index, object.length))
	      : (type == 'string' && index in object)) {
	    var other = object[index];
	    return value === value ? (value === other) : (other !== other);
	  }
	  return false;
	}

	module.exports = isIterateeCall;


/***/ },
/* 35 */
/***/ function(module, exports) {

	/** Used as the `TypeError` message for "Functions" methods. */
	var FUNC_ERROR_TEXT = 'Expected a function';

	/* Native method references for those with the same name as other `lodash` methods. */
	var nativeMax = Math.max;

	/**
	 * Creates a function that invokes `func` with the `this` binding of the
	 * created function and arguments from `start` and beyond provided as an array.
	 *
	 * **Note:** This method is based on the [rest parameter](https://developer.mozilla.org/Web/JavaScript/Reference/Functions/rest_parameters).
	 *
	 * @static
	 * @memberOf _
	 * @category Function
	 * @param {Function} func The function to apply a rest parameter to.
	 * @param {number} [start=func.length-1] The start position of the rest parameter.
	 * @returns {Function} Returns the new function.
	 * @example
	 *
	 * var say = _.restParam(function(what, names) {
	 *   return what + ' ' + _.initial(names).join(', ') +
	 *     (_.size(names) > 1 ? ', & ' : '') + _.last(names);
	 * });
	 *
	 * say('hello', 'fred', 'barney', 'pebbles');
	 * // => 'hello fred, barney, & pebbles'
	 */
	function restParam(func, start) {
	  if (typeof func != 'function') {
	    throw new TypeError(FUNC_ERROR_TEXT);
	  }
	  start = nativeMax(start === undefined ? (func.length - 1) : (+start || 0), 0);
	  return function() {
	    var args = arguments,
	        index = -1,
	        length = nativeMax(args.length - start, 0),
	        rest = Array(length);

	    while (++index < length) {
	      rest[index] = args[start + index];
	    }
	    switch (start) {
	      case 0: return func.call(this, rest);
	      case 1: return func.call(this, args[0], rest);
	      case 2: return func.call(this, args[0], args[1], rest);
	    }
	    var otherArgs = Array(start + 1);
	    index = -1;
	    while (++index < start) {
	      otherArgs[index] = args[index];
	    }
	    otherArgs[start] = rest;
	    return func.apply(this, otherArgs);
	  };
	}

	module.exports = restParam;


/***/ },
/* 36 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);
	var debugFunc = __webpack_require__(37);
	var $ = __webpack_require__(40);
	var Radio = __webpack_require__(41);

	if(window.wc_pos_debug){
	  debugFunc.enable('*');
	}

	var debug = debugFunc().enabled;
	Radio.DEBUG = debug;
	console.info(
	  'Debugging is ' +
	  ( debug ? 'on' : 'off' )  +
	  ', visit http://woopos.com.au/docs/debugging'
	);

	/**
	 * create a global variable
	 */
	module.exports = {
	  VERSION: (4004), // injected by webpack
	  attach: function(deepProperty, value){
	    deepProperty = deepProperty.split('.');
	    var nestedObj = _.reduceRight(deepProperty, function (child, parent) {
	      var obj = {};
	      obj[parent] = child;
	      return obj;
	    }, value || {});
	    $.extend(true, this, nestedObj);
	  },
	  create: function(app){
	    return _.defaults( app, this );
	  },
	  debug: debugFunc,
	  getOption: function(){}
	};

/***/ },
/* 37 */
/***/ function(module, exports, __webpack_require__) {

	
	/**
	 * This is the web browser implementation of `debug()`.
	 *
	 * Expose `debug()` as the module.
	 */

	exports = module.exports = __webpack_require__(38);
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
/* 38 */
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
	exports.humanize = __webpack_require__(39);

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
/* 39 */
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
/* 40 */
/***/ function(module, exports) {

	module.exports = jQuery;

/***/ },
/* 41 */
/***/ function(module, exports) {

	module.exports = Backbone.Radio;

/***/ },
/* 42 */
/***/ function(module, exports, __webpack_require__) {

	var Application = __webpack_require__(43);
	var bb = __webpack_require__(45);
	//var _ = require('lodash');
	var LayoutView = __webpack_require__(46);
	var debug = __webpack_require__(37)('admin');
	var accounting = __webpack_require__(48);
	var Radio = __webpack_require__(41);
	var routerChannel = Radio.channel('router');
	var polyglot = __webpack_require__(49);

	module.exports = Application.extend({

	  initialize: function() {

	    // init Root LayoutView
	    this.layout = new LayoutView();
	    this.layout.render();

	    this.listenTo(routerChannel, {
	      'before:enter:route' : this.onBeforeEnterRoute,
	      'enter:route'        : this.onEnterRoute,
	      'error:route'        : this.onErrorRoute
	    });
	  },

	  /**
	   * Set up application with start params
	   */
	  onBeforeStart: function(){
	    debug( 'starting WooCommerce POS admin app' );

	    // emulateHTTP
	    bb.emulateHTTP = this.options.emulateHTTP === true;

	    // i18n
	    polyglot.extend(this.options.i18n);

	    // bootstrap accounting settings
	    accounting.settings = this.options.accounting;
	  },

	  onStart: function(){
	    bb.history.start();
	  },

	  onBeforeEnterRoute: function() {
	    //var self = this;
	    this.transitioning = true;
	    // Don't show for synchronous route changes
	    //_.defer(function() {
	    //  if (self.transitioning) {
	    //    nprogress.start();
	    //  }
	    //});
	  },

	  onEnterRoute: function() {
	    this.transitioning = false;
	    //this.$body.scrollTop(0);
	    //nprogress.done();
	  },

	  onErrorRoute: function() {
	    this.transitioning = false;
	    //nprogress.done(true);
	  }
	});

/***/ },
/* 43 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(44);
	var POS = __webpack_require__(36);
	var Radio = __webpack_require__(41);
	var _ = __webpack_require__(2);

	module.exports = POS.Application = Mn.Application.extend({
	  _initChannel: function () {
	    this.channelName = _.result(this, 'channelName') || 'global';
	    this.channel = _.result(this, 'channel') ||
	    Radio.channel(this.channelName);
	  }
	});

/***/ },
/* 44 */
/***/ function(module, exports) {

	module.exports = Marionette;

/***/ },
/* 45 */
/***/ function(module, exports) {

	module.exports = Backbone;

/***/ },
/* 46 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(47);

	module.exports = LayoutView.extend({

	  el: '#wpbody-content .wrap',

	  template: function(){
	    return '' +
	      '<div id="wc-pos-admin"></div>' +
	      '<div id="wc-pos-modal"></div>';
	  },

	  regions: {
	    main : '#wc-pos-admin',
	    modal: '#wc-pos-modal'
	  }

	});

/***/ },
/* 47 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(44);
	var POS = __webpack_require__(36);

	module.exports = POS.LayoutView = Mn.LayoutView.extend({

	  working: function( action ) {
	    if (action === 'start') {
	      this.$el.addClass('working');
	    } else {
	      this.$el.removeClass('working');
	    }
	  }

	});

/***/ },
/* 48 */
/***/ function(module, exports) {

	module.exports = accounting;

/***/ },
/* 49 */
/***/ function(module, exports, __webpack_require__) {

	var Polyglot = __webpack_require__(50);
	var POS = __webpack_require__(36);
	var polyglot = new Polyglot();
	module.exports = POS.polyglot = polyglot;

/***/ },
/* 50 */
/***/ function(module, exports, __webpack_require__) {

	// Added for convenience in the Node environment.
	// The meat and potatoes exist in ./lib/polyglot.js.
	module.exports = __webpack_require__(51);


/***/ },
/* 51 */
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
/* 52 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var Service = __webpack_require__(53);
	var Products = __webpack_require__(54);
	var Orders = __webpack_require__(79);
	var Cart = __webpack_require__(82);
	var Customers = __webpack_require__(84);
	var Coupons = __webpack_require__(86);
	var Settings = __webpack_require__(88);
	var SettingsCollection = __webpack_require__(89);
	var Gateways = __webpack_require__(90);
	//var Variations = require('./variations/collection');
	var FilteredCollection = __webpack_require__(68);
	var debug = __webpack_require__(37)('entities');
	var POS = __webpack_require__(36);
	//var $ = require('jquery');
	var _ = __webpack_require__(2);
	var storage = global.localStorage || window.localStorage;
	var JSON = global.JSON || window.JSON;
	//var Radio = require('backbone.radio');

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
	    //variations: Variations,
	    settings  : SettingsCollection
	  },

	  getMethods: {
	    collection  : 'getCollection',
	    model       : 'getModel',
	    filtered    : 'getFiltered',
	    //variations  : 'getVariations',
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

	  //getVariations: function(options){
	  //  var parent_id = options.parent.get('id');
	  //  if( !this._variations || !this._variations[parent_id] ){
	  //    var vars = new Variations(options.parent.get('variations'), options);
	  //    this._variations = this._variations || {};
	  //    this._variations[parent_id] = new FilteredCollection(vars, options);
	  //  }
	  //  return this._variations[parent_id];
	  //},

	  idbCollections: function(){
	    return _.reduce( this.getAllCollections(), function(result, col, key){
	      if( col instanceof POS.IndexedDBCollection ){
	        result[key] = col;
	      }
	      return result;
	    }, {}, this);
	  }

	});

	module.exports = EntitiesService;
	POS.attach('Entities.Service', EntitiesService);
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 53 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(44);
	var Radio = __webpack_require__(41);
	var POS = __webpack_require__(36);
	var _ = __webpack_require__(2);

	module.exports = POS.Service = Mn.Object.extend({
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
/* 54 */
/***/ function(module, exports, __webpack_require__) {

	var DualCollection = __webpack_require__(55);
	var Model = __webpack_require__(61);

	module.exports = DualCollection.extend({
	  model: Model,
	  name: 'products'
	});

/***/ },
/* 55 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Dual Collection makes sure the data locally and the data on the server
	 * stay in sync.
	 */

	var Backbone = __webpack_require__(45);
	var Radio = Backbone.Radio;
	var debug = __webpack_require__(37)('dualCollection');
	var IDBCollection = __webpack_require__(56);
	var POS = __webpack_require__(36);
	var _ = __webpack_require__(2);
	var $ = __webpack_require__(40);
	var moment = __webpack_require__(60);

	module.exports = POS.DualCollection = IDBCollection.extend({
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
/* 56 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * TODO: merge sync/idb.js & sync/idbsync.js?
	 */

	var Collection = __webpack_require__(57);
	//var debug = require('debug')('idbCollection');
	var POS = __webpack_require__(36);
	var IndexedDB = __webpack_require__(58);
	var Radio = __webpack_require__(41);

	module.exports = POS.IndexedDBCollection = Collection.extend({
	  name          : 'store',
	  storePrefix   : 'wc_pos_',
	  dbVersion     : POS.VERSION,
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
	      },
	      onVersionChange: function(store){
	        store.clear();
	      }
	    };

	    this.db = new IndexedDB(options, this);
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
	  }

	});

/***/ },
/* 57 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(45);
	var POS = __webpack_require__(36);
	//var Radio = require('backbone.radio');

	module.exports = POS.Collection = bb.Collection.extend({
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
/* 58 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * Backbone adapter for idb-wrapper api
	 */
	var IDBStore = __webpack_require__(59);
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);
	var bb = __webpack_require__(45);
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
/* 59 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_RESULT__;/*global window:false, self:false, define:false, module:false */

	/**
	 * @license IDBWrapper - A cross-browser wrapper for IndexedDB
	 * Copyright (c) 2011 - 2015 Jens Arps
	 * http://jensarps.de/
	 *
	 * Licensed under the MIT (X11) license
	 */

	(function (name, definition, global) {
	  if (true) {
	    !(__WEBPACK_AMD_DEFINE_FACTORY__ = (definition), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.call(exports, __webpack_require__, exports, module)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	  } else if (typeof module !== 'undefined' && module.exports) {
	    module.exports = definition();
	  } else {
	    global[name] = definition();
	  }
	})('IDBStore', function () {

	  'use strict';

	  var defaultErrorHandler = function (error) {
	    throw error;
	  };
	  var defaultSuccessHandler = function () {};
	  var defaultVersionChangeHandler = function () {};

	  var defaults = {
	    storeName: 'Store',
	    storePrefix: 'IDBWrapper-',
	    dbVersion: 1,
	    keyPath: 'id',
	    autoIncrement: true,
	    onStoreReady: function () {
	    },
	    onError: defaultErrorHandler,
	    onVersionChange: defaultVersionChangeHandler,
	    indexes: []
	  };

	  /**
	   *
	   * The IDBStore constructor
	   *
	   * @constructor
	   * @name IDBStore
	   * @version 1.5
	   *
	   * @param {Object} [kwArgs] An options object used to configure the store and
	   *  set callbacks
	   * @param {String} [kwArgs.storeName='Store'] The name of the store
	   * @param {String} [kwArgs.storePrefix='IDBWrapper-'] A prefix that is
	   *  internally used to construct the name of the database, which will be
	   *  kwArgs.storePrefix + kwArgs.storeName
	   * @param {Number} [kwArgs.dbVersion=1] The version of the store
	   * @param {String} [kwArgs.keyPath='id'] The key path to use. If you want to
	   *  setup IDBWrapper to work with out-of-line keys, you need to set this to
	   *  `null`
	   * @param {Boolean} [kwArgs.autoIncrement=true] If set to true, IDBStore will
	   *  automatically make sure a unique keyPath value is present on each object
	   *  that is stored.
	   * @param {Function} [kwArgs.onStoreReady] A callback to be called when the
	   *  store is ready to be used.
	   * @param {Function} [kwArgs.onError=throw] A callback to be called when an
	   *  error occurred during instantiation of the store.
	   * @param {Array} [kwArgs.indexes=[]] An array of indexData objects
	   *  defining the indexes to use with the store. For every index to be used
	   *  one indexData object needs to be passed in the array.
	   *  An indexData object is defined as follows:
	   * @param {Object} [kwArgs.indexes.indexData] An object defining the index to
	   *  use
	   * @param {String} kwArgs.indexes.indexData.name The name of the index
	   * @param {String} [kwArgs.indexes.indexData.keyPath] The key path of the index
	   * @param {Boolean} [kwArgs.indexes.indexData.unique] Whether the index is unique
	   * @param {Boolean} [kwArgs.indexes.indexData.multiEntry] Whether the index is multi entry
	   * @param {Function} [onStoreReady] A callback to be called when the store
	   * is ready to be used.
	   * @example
	      // create a store for customers with an additional index over the
	      // `lastname` property.
	      var myCustomerStore = new IDBStore({
	        dbVersion: 1,
	        storeName: 'customer-index',
	        keyPath: 'customerid',
	        autoIncrement: true,
	        onStoreReady: populateTable,
	        indexes: [
	          { name: 'lastname', keyPath: 'lastname', unique: false, multiEntry: false }
	        ]
	      });
	   * @example
	      // create a generic store
	      var myCustomerStore = new IDBStore({
	        storeName: 'my-data-store',
	        onStoreReady: function(){
	          // start working with the store.
	        }
	      });
	   */
	  var IDBStore = function (kwArgs, onStoreReady) {

	    if (typeof onStoreReady == 'undefined' && typeof kwArgs == 'function') {
	      onStoreReady = kwArgs;
	    }
	    if (Object.prototype.toString.call(kwArgs) != '[object Object]') {
	      kwArgs = {};
	    }

	    for (var key in defaults) {
	      this[key] = typeof kwArgs[key] != 'undefined' ? kwArgs[key] : defaults[key];
	    }

	    this.dbName = this.storePrefix + this.storeName;
	    this.dbVersion = parseInt(this.dbVersion, 10) || 1;

	    onStoreReady && (this.onStoreReady = onStoreReady);

	    var env = typeof window == 'object' ? window : self;
	    this.idb = env.indexedDB || env.webkitIndexedDB || env.mozIndexedDB || env.shimIndexedDB;
	    this.keyRange = env.IDBKeyRange || env.webkitIDBKeyRange || env.mozIDBKeyRange;

	    this.features = {
	      hasAutoIncrement: !env.mozIndexedDB
	    };

	    this.consts = {
	      'READ_ONLY':         'readonly',
	      'READ_WRITE':        'readwrite',
	      'VERSION_CHANGE':    'versionchange',
	      'NEXT':              'next',
	      'NEXT_NO_DUPLICATE': 'nextunique',
	      'PREV':              'prev',
	      'PREV_NO_DUPLICATE': 'prevunique'
	    };

	    this.openDB();
	  };

	  IDBStore.prototype = /** @lends IDBStore */ {

	    /**
	     * A pointer to the IDBStore ctor
	     *
	     * @type IDBStore
	     */
	    constructor: IDBStore,

	    /**
	     * The version of IDBStore
	     *
	     * @type String
	     */
	    version: '1.5',

	    /**
	     * A reference to the IndexedDB object
	     *
	     * @type Object
	     */
	    db: null,

	    /**
	     * The full name of the IndexedDB used by IDBStore, composed of
	     * this.storePrefix + this.storeName
	     *
	     * @type String
	     */
	    dbName: null,

	    /**
	     * The version of the IndexedDB used by IDBStore
	     *
	     * @type Number
	     */
	    dbVersion: null,

	    /**
	     * A reference to the objectStore used by IDBStore
	     *
	     * @type Object
	     */
	    store: null,

	    /**
	     * The store name
	     *
	     * @type String
	     */
	    storeName: null,

	    /**
	     * The prefix to prepend to the store name
	     *
	     * @type String
	     */
	    storePrefix: null,

	    /**
	     * The key path
	     *
	     * @type String
	     */
	    keyPath: null,

	    /**
	     * Whether IDBStore uses autoIncrement
	     *
	     * @type Boolean
	     */
	    autoIncrement: null,

	    /**
	     * The indexes used by IDBStore
	     *
	     * @type Array
	     */
	    indexes: null,

	    /**
	     * A hashmap of features of the used IDB implementation
	     *
	     * @type Object
	     * @proprty {Boolean} autoIncrement If the implementation supports
	     *  native auto increment
	     */
	    features: null,

	    /**
	     * The callback to be called when the store is ready to be used
	     *
	     * @type Function
	     */
	    onStoreReady: null,

	    /**
	     * The callback to be called if an error occurred during instantiation
	     * of the store
	     *
	     * @type Function
	     */
	    onError: null,

	    /**
	     * The internal insertID counter
	     *
	     * @type Number
	     * @private
	     */
	    _insertIdCount: 0,

	    /**
	     * Opens an IndexedDB; called by the constructor.
	     *
	     * Will check if versions match and compare provided index configuration
	     * with existing ones, and update indexes if necessary.
	     *
	     * Will call this.onStoreReady() if everything went well and the store
	     * is ready to use, and this.onError() is something went wrong.
	     *
	     * @private
	     *
	     */
	    openDB: function () {

	      var openRequest = this.idb.open(this.dbName, this.dbVersion);
	      var preventSuccessCallback = false;

	      openRequest.onerror = function (error) {

	        var gotVersionErr = false;
	        if ('error' in error.target) {
	          gotVersionErr = error.target.error.name == 'VersionError';
	        } else if ('errorCode' in error.target) {
	          gotVersionErr = error.target.errorCode == 12;
	        }

	        if (gotVersionErr) {
	          this.onError(new Error('The version number provided is lower than the existing one.'));
	        } else {
	          this.onError(error);
	        }
	      }.bind(this);

	      openRequest.onsuccess = function (event) {

	        if (preventSuccessCallback) {
	          return;
	        }

	        if(this.db){
	          this.onStoreReady();
	          return;
	        }

	        this.db = event.target.result;

	        if(typeof this.db.version == 'string'){
	          this.onError(new Error('The IndexedDB implementation in this browser is outdated. Please upgrade your browser.'));
	          return;
	        }

	        if(!this.db.objectStoreNames.contains(this.storeName)){
	          // We should never ever get here.
	          // Lets notify the user anyway.
	          this.onError(new Error('Something is wrong with the IndexedDB implementation in this browser. Please upgrade your browser.'));
	          return;
	        }

	        var emptyTransaction = this.db.transaction([this.storeName], this.consts.READ_ONLY);
	        this.store = emptyTransaction.objectStore(this.storeName);

	        // check indexes
	        var existingIndexes = Array.prototype.slice.call(this.getIndexList());
	        this.indexes.forEach(function(indexData){
	          var indexName = indexData.name;

	          if(!indexName){
	            preventSuccessCallback = true;
	            this.onError(new Error('Cannot create index: No index name given.'));
	            return;
	          }

	          this.normalizeIndexData(indexData);

	          if(this.hasIndex(indexName)){
	            // check if it complies
	            var actualIndex = this.store.index(indexName);
	            var complies = this.indexComplies(actualIndex, indexData);
	            if(!complies){
	              preventSuccessCallback = true;
	              this.onError(new Error('Cannot modify index "' + indexName + '" for current version. Please bump version number to ' + ( this.dbVersion + 1 ) + '.'));
	            }

	            existingIndexes.splice(existingIndexes.indexOf(indexName), 1);
	          } else {
	            preventSuccessCallback = true;
	            this.onError(new Error('Cannot create new index "' + indexName + '" for current version. Please bump version number to ' + ( this.dbVersion + 1 ) + '.'));
	          }

	        }, this);

	        if (existingIndexes.length) {
	          preventSuccessCallback = true;
	          this.onError(new Error('Cannot delete index(es) "' + existingIndexes.toString() + '" for current version. Please bump version number to ' + ( this.dbVersion + 1 ) + '.'));
	        }

	        preventSuccessCallback || this.onStoreReady();
	      }.bind(this);

	      openRequest.onupgradeneeded = function(/* IDBVersionChangeEvent */ event){

	        this.db = event.target.result;

	        if(this.db.objectStoreNames.contains(this.storeName)){
	          this.store = event.target.transaction.objectStore(this.storeName);
	        } else {
	          var optionalParameters = { autoIncrement: this.autoIncrement };
	          if (this.keyPath !== null) {
	            optionalParameters.keyPath = this.keyPath;
	          }
	          this.store = this.db.createObjectStore(this.storeName, optionalParameters);
	        }

	        /**
	         * Trigger onVersionChange method
	         */
	        this.onVersionChange(this.store, event);

	        var existingIndexes = Array.prototype.slice.call(this.getIndexList());
	        this.indexes.forEach(function(indexData){
	          var indexName = indexData.name;

	          if(!indexName){
	            preventSuccessCallback = true;
	            this.onError(new Error('Cannot create index: No index name given.'));
	          }

	          this.normalizeIndexData(indexData);

	          if(this.hasIndex(indexName)){
	            // check if it complies
	            var actualIndex = this.store.index(indexName);
	            var complies = this.indexComplies(actualIndex, indexData);
	            if(!complies){
	              // index differs, need to delete and re-create
	              this.store.deleteIndex(indexName);
	              this.store.createIndex(indexName, indexData.keyPath, { unique: indexData.unique, multiEntry: indexData.multiEntry });
	            }

	            existingIndexes.splice(existingIndexes.indexOf(indexName), 1);
	          } else {
	            this.store.createIndex(indexName, indexData.keyPath, { unique: indexData.unique, multiEntry: indexData.multiEntry });
	          }

	        }, this);

	        if (existingIndexes.length) {
	          existingIndexes.forEach(function(_indexName){
	            this.store.deleteIndex(_indexName);
	          }, this);
	        }

	      }.bind(this);
	    },

	    /**
	     * Deletes the database used for this store if the IDB implementations
	     * provides that functionality.
	     *
	     * @param {Function} [onSuccess] A callback that is called if deletion
	     *  was successful.
	     * @param {Function} [onError] A callback that is called if deletion
	     *  failed.
	     */
	    deleteDatabase: function (onSuccess, onError) {
	      if (this.idb.deleteDatabase) {
	        this.db.close();
	        var deleteRequest = this.idb.deleteDatabase(this.dbName);
	        deleteRequest.onsuccess = onSuccess;
	        deleteRequest.onerror = onError;
	      } else {
	        onError(new Error('Browser does not support IndexedDB deleteDatabase!'));
	      }
	    },

	    /*********************
	     * data manipulation *
	     *********************/

	    /**
	     * Puts an object into the store. If an entry with the given id exists,
	     * it will be overwritten. This method has a different signature for inline
	     * keys and out-of-line keys; please see the examples below.
	     *
	     * @param {*} [key] The key to store. This is only needed if IDBWrapper
	     *  is set to use out-of-line keys. For inline keys - the default scenario -
	     *  this can be omitted.
	     * @param {Object} value The data object to store.
	     * @param {Function} [onSuccess] A callback that is called if insertion
	     *  was successful.
	     * @param {Function} [onError] A callback that is called if insertion
	     *  failed.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     * @example
	        // Storing an object, using inline keys (the default scenario):
	        var myCustomer = {
	          customerid: 2346223,
	          lastname: 'Doe',
	          firstname: 'John'
	        };
	        myCustomerStore.put(myCustomer, mySuccessHandler, myErrorHandler);
	        // Note that passing success- and error-handlers is optional.
	     * @example
	        // Storing an object, using out-of-line keys:
	       var myCustomer = {
	         lastname: 'Doe',
	         firstname: 'John'
	       };
	       myCustomerStore.put(2346223, myCustomer, mySuccessHandler, myErrorHandler);
	      // Note that passing success- and error-handlers is optional.
	     */
	    put: function (key, value, onSuccess, onError) {
	      if (this.keyPath !== null) {
	        onError = onSuccess;
	        onSuccess = value;
	        value = key;
	      }
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);

	      var hasSuccess = false,
	          result = null,
	          putRequest;

	      var putTransaction = this.db.transaction([this.storeName], this.consts.READ_WRITE);
	      putTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      putTransaction.onabort = onError;
	      putTransaction.onerror = onError;

	      if (this.keyPath !== null) { // in-line keys
	        this._addIdPropertyIfNeeded(value);
	        putRequest = putTransaction.objectStore(this.storeName).put(value);
	      } else { // out-of-line keys
	        putRequest = putTransaction.objectStore(this.storeName).put(value, key);
	      }
	      putRequest.onsuccess = function (event) {
	        hasSuccess = true;
	        result = event.target.result;
	      };
	      putRequest.onerror = onError;

	      return putTransaction;
	    },

	    /**
	     * Retrieves an object from the store. If no entry exists with the given id,
	     * the success handler will be called with null as first and only argument.
	     *
	     * @param {*} key The id of the object to fetch.
	     * @param {Function} [onSuccess] A callback that is called if fetching
	     *  was successful. Will receive the object as only argument.
	     * @param {Function} [onError] A callback that will be called if an error
	     *  occurred during the operation.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    get: function (key, onSuccess, onError) {
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);

	      var hasSuccess = false,
	          result = null;
	      
	      var getTransaction = this.db.transaction([this.storeName], this.consts.READ_ONLY);
	      getTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      getTransaction.onabort = onError;
	      getTransaction.onerror = onError;
	      var getRequest = getTransaction.objectStore(this.storeName).get(key);
	      getRequest.onsuccess = function (event) {
	        hasSuccess = true;
	        result = event.target.result;
	      };
	      getRequest.onerror = onError;

	      return getTransaction;
	    },

	    /**
	     * Removes an object from the store.
	     *
	     * @param {*} key The id of the object to remove.
	     * @param {Function} [onSuccess] A callback that is called if the removal
	     *  was successful.
	     * @param {Function} [onError] A callback that will be called if an error
	     *  occurred during the operation.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    remove: function (key, onSuccess, onError) {
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);

	      var hasSuccess = false,
	          result = null;

	      var removeTransaction = this.db.transaction([this.storeName], this.consts.READ_WRITE);
	      removeTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      removeTransaction.onabort = onError;
	      removeTransaction.onerror = onError;

	      var deleteRequest = removeTransaction.objectStore(this.storeName)['delete'](key);
	      deleteRequest.onsuccess = function (event) {
	        hasSuccess = true;
	        result = event.target.result;
	      };
	      deleteRequest.onerror = onError;

	      return removeTransaction;
	    },

	    /**
	     * Runs a batch of put and/or remove operations on the store.
	     *
	     * @param {Array} dataArray An array of objects containing the operation to run
	     *  and the data object (for put operations).
	     * @param {Function} [onSuccess] A callback that is called if all operations
	     *  were successful.
	     * @param {Function} [onError] A callback that is called if an error
	     *  occurred during one of the operations.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    batch: function (dataArray, onSuccess, onError) {
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);

	      if(Object.prototype.toString.call(dataArray) != '[object Array]'){
	        onError(new Error('dataArray argument must be of type Array.'));
	      }
	      var batchTransaction = this.db.transaction([this.storeName] , this.consts.READ_WRITE);
	      batchTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(hasSuccess);
	      };
	      batchTransaction.onabort = onError;
	      batchTransaction.onerror = onError;
	      
	      var count = dataArray.length;
	      var called = false;
	      var hasSuccess = false;

	      var onItemSuccess = function () {
	        count--;
	        if (count === 0 && !called) {
	          called = true;
	          hasSuccess = true;
	        }
	      };

	      dataArray.forEach(function (operation) {
	        var type = operation.type;
	        var key = operation.key;
	        var value = operation.value;

	        var onItemError = function (err) {
	          batchTransaction.abort();
	          if (!called) {
	            called = true;
	            onError(err, type, key);
	          }
	        };

	        if (type == 'remove') {
	          var deleteRequest = batchTransaction.objectStore(this.storeName)['delete'](key);
	          deleteRequest.onsuccess = onItemSuccess;
	          deleteRequest.onerror = onItemError;
	        } else if (type == 'put') {
	          var putRequest;
	          if (this.keyPath !== null) { // in-line keys
	            this._addIdPropertyIfNeeded(value);
	            putRequest = batchTransaction.objectStore(this.storeName).put(value);
	          } else { // out-of-line keys
	            putRequest = batchTransaction.objectStore(this.storeName).put(value, key);
	          }
	          putRequest.onsuccess = onItemSuccess;
	          putRequest.onerror = onItemError;
	        }
	      }, this);

	      return batchTransaction;
	    },

	    /**
	     * Takes an array of objects and stores them in a single transaction.
	     *
	     * @param {Array} dataArray An array of objects to store
	     * @param {Function} [onSuccess] A callback that is called if all operations
	     *  were successful.
	     * @param {Function} [onError] A callback that is called if an error
	     *  occurred during one of the operations.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    putBatch: function (dataArray, onSuccess, onError) {
	      var batchData = dataArray.map(function(item){
	        return { type: 'put', value: item };
	      });

	      return this.batch(batchData, onSuccess, onError);
	    },

	    /**
	     * Like putBatch, takes an array of objects and stores them in a single
	     * transaction, but allows processing of the result values.  Returns the
	     * processed records containing the key for newly created records to the
	     * onSuccess calllback instead of only returning true or false for success.
	     * In addition, added the option for the caller to specify a key field that
	     * should be set to the newly created key.
	     *
	     * @param {Array} dataArray An array of objects to store
	     * @param {Object} [options] An object containing optional options
	     * @param {String} [options.keyField=this.keyPath] Specifies a field in the record to update
	     *  with the auto-incrementing key. Defaults to the store's keyPath.
	     * @param {Function} [onSuccess] A callback that is called if all operations
	     *  were successful.
	     * @param {Function} [onError] A callback that is called if an error
	     *  occurred during one of the operations.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     *
	     */
	    upsertBatch: function (dataArray, options, onSuccess, onError) {
	      // handle `dataArray, onSuccess, onError` signature
	      if (typeof options == 'function') {
	        onSuccess = options;
	        onError = onSuccess;
	        options = {};
	      }

	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);
	      options || (options = {});

	      if (Object.prototype.toString.call(dataArray) != '[object Array]') {
	        onError(new Error('dataArray argument must be of type Array.'));
	      }
	      var batchTransaction = this.db.transaction([this.storeName], this.consts.READ_WRITE);
	      batchTransaction.oncomplete = function () {
	        if (hasSuccess) {
	          onSuccess(dataArray);
	        } else {
	          onError(false);
	        }
	      };
	      batchTransaction.onabort = onError;
	      batchTransaction.onerror = onError;

	      var keyField = options.keyField || this.keyPath;
	      var count = dataArray.length;
	      var called = false;
	      var hasSuccess = false;
	      var index = 0; // assume success callbacks are executed in order

	      var onItemSuccess = function (event) {
	        var record = dataArray[index++];
	        record[keyField] = event.target.result;

	        count--;
	        if (count === 0 && !called) {
	          called = true;
	          hasSuccess = true;
	        }
	      };

	      dataArray.forEach(function (record) {
	        var key = record.key;

	        var onItemError = function (err) {
	          batchTransaction.abort();
	          if (!called) {
	            called = true;
	            onError(err);
	          }
	        };

	        var putRequest;
	        if (this.keyPath !== null) { // in-line keys
	          this._addIdPropertyIfNeeded(record);
	          putRequest = batchTransaction.objectStore(this.storeName).put(record);
	        } else { // out-of-line keys
	          putRequest = batchTransaction.objectStore(this.storeName).put(record, key);
	        }
	        putRequest.onsuccess = onItemSuccess;
	        putRequest.onerror = onItemError;
	      }, this);

	      return batchTransaction;
	    },

	    /**
	     * Takes an array of keys and removes matching objects in a single
	     * transaction.
	     *
	     * @param {Array} keyArray An array of keys to remove
	     * @param {Function} [onSuccess] A callback that is called if all operations
	     *  were successful.
	     * @param {Function} [onError] A callback that is called if an error
	     *  occurred during one of the operations.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    removeBatch: function (keyArray, onSuccess, onError) {
	      var batchData = keyArray.map(function(key){
	        return { type: 'remove', key: key };
	      });

	      return this.batch(batchData, onSuccess, onError);
	    },

	    /**
	     * Takes an array of keys and fetches matching objects
	     *
	     * @param {Array} keyArray An array of keys identifying the objects to fetch
	     * @param {Function} [onSuccess] A callback that is called if all operations
	     *  were successful.
	     * @param {Function} [onError] A callback that is called if an error
	     *  occurred during one of the operations.
	     * @param {String} [arrayType='sparse'] The type of array to pass to the
	     *  success handler. May be one of 'sparse', 'dense' or 'skip'. Defaults to
	     *  'sparse'. This parameter specifies how to handle the situation if a get
	     *  operation did not throw an error, but there was no matching object in
	     *  the database. In most cases, 'sparse' provides the most desired
	     *  behavior. See the examples for details.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     * @example
	     // given that there are two objects in the database with the keypath
	     // values 1 and 2, and the call looks like this:
	     myStore.getBatch([1, 5, 2], onError, function (data) { … }, arrayType);

	     // this is what the `data` array will be like:

	     // arrayType == 'sparse':
	     // data is a sparse array containing two entries and having a length of 3:
	       [Object, 2: Object]
	         0: Object
	         2: Object
	         length: 3
	         __proto__: Array[0]
	     // calling forEach on data will result in the callback being called two
	     // times, with the index parameter matching the index of the key in the
	     // keyArray.

	     // arrayType == 'dense':
	     // data is a dense array containing three entries and having a length of 3,
	     // where data[1] is of type undefined:
	       [Object, undefined, Object]
	         0: Object
	         1: undefined
	         2: Object
	         length: 3
	         __proto__: Array[0]
	     // calling forEach on data will result in the callback being called three
	     // times, with the index parameter matching the index of the key in the
	     // keyArray, but the second call will have undefined as first argument.

	     // arrayType == 'skip':
	     // data is a dense array containing two entries and having a length of 2:
	       [Object, Object]
	         0: Object
	         1: Object
	         length: 2
	         __proto__: Array[0]
	     // calling forEach on data will result in the callback being called two
	     // times, with the index parameter not matching the index of the key in the
	     // keyArray.
	     */
	    getBatch: function (keyArray, onSuccess, onError, arrayType) {
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);
	      arrayType || (arrayType = 'sparse');

	      if(Object.prototype.toString.call(keyArray) != '[object Array]'){
	        onError(new Error('keyArray argument must be of type Array.'));
	      }
	      var batchTransaction = this.db.transaction([this.storeName] , this.consts.READ_ONLY);
	      batchTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      batchTransaction.onabort = onError;
	      batchTransaction.onerror = onError;

	      var data = [];
	      var count = keyArray.length;
	      var called = false;
	      var hasSuccess = false;
	      var result = null;

	      var onItemSuccess = function (event) {
	        if (event.target.result || arrayType == 'dense') {
	          data.push(event.target.result);
	        } else if (arrayType == 'sparse') {
	          data.length++;
	        }
	        count--;
	        if (count === 0) {
	          called = true;
	          hasSuccess = true;
	          result = data;
	        }
	      };

	      keyArray.forEach(function (key) {

	        var onItemError = function (err) {
	          called = true;
	          result = err;
	          onError(err);
	          batchTransaction.abort();
	        };

	        var getRequest = batchTransaction.objectStore(this.storeName).get(key);
	        getRequest.onsuccess = onItemSuccess;
	        getRequest.onerror = onItemError;

	      }, this);

	      return batchTransaction;
	    },

	    /**
	     * Fetches all entries in the store.
	     *
	     * @param {Function} [onSuccess] A callback that is called if the operation
	     *  was successful. Will receive an array of objects.
	     * @param {Function} [onError] A callback that will be called if an error
	     *  occurred during the operation.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    getAll: function (onSuccess, onError) {
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);
	      var getAllTransaction = this.db.transaction([this.storeName], this.consts.READ_ONLY);
	      var store = getAllTransaction.objectStore(this.storeName);
	      if (store.getAll) {
	        this._getAllNative(getAllTransaction, store, onSuccess, onError);
	      } else {
	        this._getAllCursor(getAllTransaction, store, onSuccess, onError);
	      }

	      return getAllTransaction;
	    },

	    /**
	     * Implements getAll for IDB implementations that have a non-standard
	     * getAll() method.
	     *
	     * @param {Object} getAllTransaction An open READ transaction.
	     * @param {Object} store A reference to the store.
	     * @param {Function} onSuccess A callback that will be called if the
	     *  operation was successful.
	     * @param {Function} onError A callback that will be called if an
	     *  error occurred during the operation.
	     * @private
	     */
	    _getAllNative: function (getAllTransaction, store, onSuccess, onError) {
	      var hasSuccess = false,
	          result = null;

	      getAllTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      getAllTransaction.onabort = onError;
	      getAllTransaction.onerror = onError;

	      var getAllRequest = store.getAll();
	      getAllRequest.onsuccess = function (event) {
	        hasSuccess = true;
	        result = event.target.result;
	      };
	      getAllRequest.onerror = onError;
	    },

	    /**
	     * Implements getAll for IDB implementations that do not have a getAll()
	     * method.
	     *
	     * @param {Object} getAllTransaction An open READ transaction.
	     * @param {Object} store A reference to the store.
	     * @param {Function} onSuccess A callback that will be called if the
	     *  operation was successful.
	     * @param {Function} onError A callback that will be called if an
	     *  error occurred during the operation.
	     * @private
	     */
	    _getAllCursor: function (getAllTransaction, store, onSuccess, onError) {
	      var all = [],
	          hasSuccess = false,
	          result = null;

	      getAllTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      getAllTransaction.onabort = onError;
	      getAllTransaction.onerror = onError;

	      var cursorRequest = store.openCursor();
	      cursorRequest.onsuccess = function (event) {
	        var cursor = event.target.result;
	        if (cursor) {
	          all.push(cursor.value);
	          cursor['continue']();
	        }
	        else {
	          hasSuccess = true;
	          result = all;
	        }
	      };
	      cursorRequest.onError = onError;
	    },

	    /**
	     * Clears the store, i.e. deletes all entries in the store.
	     *
	     * @param {Function} [onSuccess] A callback that will be called if the
	     *  operation was successful.
	     * @param {Function} [onError] A callback that will be called if an
	     *  error occurred during the operation.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    clear: function (onSuccess, onError) {
	      onError || (onError = defaultErrorHandler);
	      onSuccess || (onSuccess = defaultSuccessHandler);

	      var hasSuccess = false,
	          result = null;

	      var clearTransaction = this.db.transaction([this.storeName], this.consts.READ_WRITE);
	      clearTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      clearTransaction.onabort = onError;
	      clearTransaction.onerror = onError;

	      var clearRequest = clearTransaction.objectStore(this.storeName).clear();
	      clearRequest.onsuccess = function (event) {
	        hasSuccess = true;
	        result = event.target.result;
	      };
	      clearRequest.onerror = onError;

	      return clearTransaction;
	    },

	    /**
	     * Checks if an id property needs to present on a object and adds one if
	     * necessary.
	     *
	     * @param {Object} dataObj The data object that is about to be stored
	     * @private
	     */
	    _addIdPropertyIfNeeded: function (dataObj) {
	      if (!this.features.hasAutoIncrement && typeof dataObj[this.keyPath] == 'undefined') {
	        dataObj[this.keyPath] = this._insertIdCount++ + Date.now();
	      }
	    },

	    /************
	     * indexing *
	     ************/

	    /**
	     * Returns a DOMStringList of index names of the store.
	     *
	     * @return {DOMStringList} The list of index names
	     */
	    getIndexList: function () {
	      return this.store.indexNames;
	    },

	    /**
	     * Checks if an index with the given name exists in the store.
	     *
	     * @param {String} indexName The name of the index to look for
	     * @return {Boolean} Whether the store contains an index with the given name
	     */
	    hasIndex: function (indexName) {
	      return this.store.indexNames.contains(indexName);
	    },

	    /**
	     * Normalizes an object containing index data and assures that all
	     * properties are set.
	     *
	     * @param {Object} indexData The index data object to normalize
	     * @param {String} indexData.name The name of the index
	     * @param {String} [indexData.keyPath] The key path of the index
	     * @param {Boolean} [indexData.unique] Whether the index is unique
	     * @param {Boolean} [indexData.multiEntry] Whether the index is multi entry
	     */
	    normalizeIndexData: function (indexData) {
	      indexData.keyPath = indexData.keyPath || indexData.name;
	      indexData.unique = !!indexData.unique;
	      indexData.multiEntry = !!indexData.multiEntry;
	    },

	    /**
	     * Checks if an actual index complies with an expected index.
	     *
	     * @param {Object} actual The actual index found in the store
	     * @param {Object} expected An Object describing an expected index
	     * @return {Boolean} Whether both index definitions are identical
	     */
	    indexComplies: function (actual, expected) {
	      var complies = ['keyPath', 'unique', 'multiEntry'].every(function (key) {
	        // IE10 returns undefined for no multiEntry
	        if (key == 'multiEntry' && actual[key] === undefined && expected[key] === false) {
	          return true;
	        }
	        // Compound keys
	        if (key == 'keyPath' && Object.prototype.toString.call(expected[key]) == '[object Array]') {
	          var exp = expected.keyPath;
	          var act = actual.keyPath;

	          // IE10 can't handle keyPath sequences and stores them as a string.
	          // The index will be unusable there, but let's still return true if
	          // the keyPath sequence matches.
	          if (typeof act == 'string') {
	            return exp.toString() == act;
	          }

	          // Chrome/Opera stores keyPath squences as DOMStringList, Firefox
	          // as Array
	          if ( ! (typeof act.contains == 'function' || typeof act.indexOf == 'function') ) {
	            return false;
	          }

	          if (act.length !== exp.length) {
	            return false;
	          }

	          for (var i = 0, m = exp.length; i<m; i++) {
	            if ( ! ( (act.contains && act.contains(exp[i])) || act.indexOf(exp[i] !== -1) )) {
	              return false;
	            }
	          }
	          return true;
	        }
	        return expected[key] == actual[key];
	      });
	      return complies;
	    },

	    /**********
	     * cursor *
	     **********/

	    /**
	     * Iterates over the store using the given options and calling onItem
	     * for each entry matching the options.
	     *
	     * @param {Function} onItem A callback to be called for each match
	     * @param {Object} [options] An object defining specific options
	     * @param {Object} [options.index=null] An IDBIndex to operate on
	     * @param {String} [options.order=ASC] The order in which to provide the
	     *  results, can be 'DESC' or 'ASC'
	     * @param {Boolean} [options.autoContinue=true] Whether to automatically
	     *  iterate the cursor to the next result
	     * @param {Boolean} [options.filterDuplicates=false] Whether to exclude
	     *  duplicate matches
	     * @param {Object} [options.keyRange=null] An IDBKeyRange to use
	     * @param {Boolean} [options.writeAccess=false] Whether grant write access
	     *  to the store in the onItem callback
	     * @param {Function} [options.onEnd=null] A callback to be called after
	     *  iteration has ended
	     * @param {Function} [options.onError=throw] A callback to be called
	     *  if an error occurred during the operation.
	     * @param {Number} [options.limit=Infinity] Limit the number of returned
	     *  results to this number
	     * @param {Number} [options.offset=0] Skip the provided number of results
	     *  in the resultset
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    iterate: function (onItem, options) {
	      options = mixin({
	        index: null,
	        order: 'ASC',
	        autoContinue: true,
	        filterDuplicates: false,
	        keyRange: null,
	        writeAccess: false,
	        onEnd: null,
	        onError: defaultErrorHandler,
	        limit: Infinity,
	        offset: 0
	      }, options || {});

	      var directionType = options.order.toLowerCase() == 'desc' ? 'PREV' : 'NEXT';
	      if (options.filterDuplicates) {
	        directionType += '_NO_DUPLICATE';
	      }

	      var hasSuccess = false;
	      var cursorTransaction = this.db.transaction([this.storeName], this.consts[options.writeAccess ? 'READ_WRITE' : 'READ_ONLY']);
	      var cursorTarget = cursorTransaction.objectStore(this.storeName);
	      if (options.index) {
	        cursorTarget = cursorTarget.index(options.index);
	      }
	      var recordCount = 0;

	      cursorTransaction.oncomplete = function () {
	        if (!hasSuccess) {
	          options.onError(null);
	          return;
	        }
	        if (options.onEnd) {
	          options.onEnd();
	        } else {
	          onItem(null);
	        }
	      };
	      cursorTransaction.onabort = options.onError;
	      cursorTransaction.onerror = options.onError;

	      var cursorRequest = cursorTarget.openCursor(options.keyRange, this.consts[directionType]);
	      cursorRequest.onerror = options.onError;
	      cursorRequest.onsuccess = function (event) {
	        var cursor = event.target.result;
	        if (cursor) {
	          if (options.offset) {
	            cursor.advance(options.offset);
	            options.offset = 0;
	          } else {
	            onItem(cursor.value, cursor, cursorTransaction);
	            recordCount++;
	            if (options.autoContinue) {
	              if (recordCount + options.offset < options.limit) {
	                cursor['continue']();
	              } else {
	                hasSuccess = true;
	              }
	            }
	          }
	        } else {
	          hasSuccess = true;
	        }
	      };

	      return cursorTransaction;
	    },

	    /**
	     * Runs a query against the store and passes an array containing matched
	     * objects to the success handler.
	     *
	     * @param {Function} onSuccess A callback to be called when the operation
	     *  was successful.
	     * @param {Object} [options] An object defining specific options
	     * @param {Object} [options.index=null] An IDBIndex to operate on
	     * @param {String} [options.order=ASC] The order in which to provide the
	     *  results, can be 'DESC' or 'ASC'
	     * @param {Boolean} [options.filterDuplicates=false] Whether to exclude
	     *  duplicate matches
	     * @param {Object} [options.keyRange=null] An IDBKeyRange to use
	     * @param {Function} [options.onError=throw] A callback to be called
	     *  if an error occurred during the operation.
	     * @param {Number} [options.limit=Infinity] Limit the number of returned
	     *  results to this number
	     * @param {Number} [options.offset=0] Skip the provided number of results
	     *  in the resultset
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    query: function (onSuccess, options) {
	      var result = [];
	      options = options || {};
	      options.autoContinue = true;
	      options.writeAccess = false;
	      options.onEnd = function () {
	        onSuccess(result);
	      };
	      return this.iterate(function (item) {
	        result.push(item);
	      }, options);
	    },

	    /**
	     *
	     * Runs a query against the store, but only returns the number of matches
	     * instead of the matches itself.
	     *
	     * @param {Function} onSuccess A callback to be called if the opration
	     *  was successful.
	     * @param {Object} [options] An object defining specific options
	     * @param {Object} [options.index=null] An IDBIndex to operate on
	     * @param {Object} [options.keyRange=null] An IDBKeyRange to use
	     * @param {Function} [options.onError=throw] A callback to be called if an error
	     *  occurred during the operation.
	     * @returns {IDBTransaction} The transaction used for this operation.
	     */
	    count: function (onSuccess, options) {

	      options = mixin({
	        index: null,
	        keyRange: null
	      }, options || {});

	      var onError = options.onError || defaultErrorHandler;

	      var hasSuccess = false,
	          result = null;

	      var cursorTransaction = this.db.transaction([this.storeName], this.consts.READ_ONLY);
	      cursorTransaction.oncomplete = function () {
	        var callback = hasSuccess ? onSuccess : onError;
	        callback(result);
	      };
	      cursorTransaction.onabort = onError;
	      cursorTransaction.onerror = onError;

	      var cursorTarget = cursorTransaction.objectStore(this.storeName);
	      if (options.index) {
	        cursorTarget = cursorTarget.index(options.index);
	      }
	      var countRequest = cursorTarget.count(options.keyRange);
	      countRequest.onsuccess = function (evt) {
	        hasSuccess = true;
	        result = evt.target.result;
	      };
	      countRequest.onError = onError;

	      return cursorTransaction;
	    },

	    /**************/
	    /* key ranges */
	    /**************/

	    /**
	     * Creates a key range using specified options. This key range can be
	     * handed over to the count() and iterate() methods.
	     *
	     * Note: You must provide at least one or both of "lower" or "upper" value.
	     *
	     * @param {Object} options The options for the key range to create
	     * @param {*} [options.lower] The lower bound
	     * @param {Boolean} [options.excludeLower] Whether to exclude the lower
	     *  bound passed in options.lower from the key range
	     * @param {*} [options.upper] The upper bound
	     * @param {Boolean} [options.excludeUpper] Whether to exclude the upper
	     *  bound passed in options.upper from the key range
	     * @param {*} [options.only] A single key value. Use this if you need a key
	     *  range that only includes one value for a key. Providing this
	     *  property invalidates all other properties.
	     * @return {Object} The IDBKeyRange representing the specified options
	     */
	    makeKeyRange: function(options){
	      /*jshint onecase:true */
	      var keyRange,
	          hasLower = typeof options.lower != 'undefined',
	          hasUpper = typeof options.upper != 'undefined',
	          isOnly = typeof options.only != 'undefined';

	      switch(true){
	        case isOnly:
	          keyRange = this.keyRange.only(options.only);
	          break;
	        case hasLower && hasUpper:
	          keyRange = this.keyRange.bound(options.lower, options.upper, options.excludeLower, options.excludeUpper);
	          break;
	        case hasLower:
	          keyRange = this.keyRange.lowerBound(options.lower, options.excludeLower);
	          break;
	        case hasUpper:
	          keyRange = this.keyRange.upperBound(options.upper, options.excludeUpper);
	          break;
	        default:
	          throw new Error('Cannot create KeyRange. Provide one or both of "lower" or "upper" value, or an "only" value.');
	      }

	      return keyRange;

	    }

	  };

	  /** helpers **/

	  // TODO: Check Object.create support to get rid of this
	  var empty = {};
	  var mixin = function (target, source) {
	    var name, s;
	    for (name in source) {
	      s = source[name];
	      if (s !== empty[name] && s !== target[name]) {
	        target[name] = s;
	      }
	    }
	    return target;
	  };

	  IDBStore.version = IDBStore.prototype.version;

	  return IDBStore;

	}, this);


/***/ },
/* 60 */
/***/ function(module, exports) {

	module.exports = moment;

/***/ },
/* 61 */
/***/ function(module, exports, __webpack_require__) {

	var DualModel = __webpack_require__(62);
	var _ = __webpack_require__(2);
	var Variations = __webpack_require__(66);
	var FilteredCollection = __webpack_require__(68);

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
/* 62 */
/***/ function(module, exports, __webpack_require__) {

	var DeepModel = __webpack_require__(63);
	var POS = __webpack_require__(36);
	var _ = __webpack_require__(2);
	var debug = __webpack_require__(37)('dualModel');

	module.exports = POS.DualModel = DeepModel.extend({
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
/* 63 */
/***/ function(module, exports, __webpack_require__) {

	var POS = __webpack_require__(36);
	var Model = __webpack_require__(64);
	var DeepModel = __webpack_require__(65);

	module.exports = POS.DeepModel = Model.extend(DeepModel);

/***/ },
/* 64 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(45);
	var POS = __webpack_require__(36);
	var _ = __webpack_require__(2);
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

	module.exports = POS.Model = bb.Model.extend({

	  constructor: function() {
	    bb.Model.apply(this, arguments);
	  },

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
/* 65 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);

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
/* 66 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(57);
	var Model = __webpack_require__(67);
	var Radio = __webpack_require__(41);
	var _ = __webpack_require__(2);

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
/* 67 */
/***/ function(module, exports, __webpack_require__) {

	//var Model = require('lib/config/model');
	var Model = __webpack_require__(63);

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
/* 68 */
/***/ function(module, exports, __webpack_require__) {

	/* jshint -W071, -W003, -W021 */
	var _ = __webpack_require__(2);
	var Backbone = __webpack_require__(45);
	var FilteredCollection = __webpack_require__(69);
	var SortedCollection = __webpack_require__(72);
	var PaginatedCollection = __webpack_require__(74);
	var proxyCollection = __webpack_require__(70);
	var proxyEvents = __webpack_require__(75);
	var query = __webpack_require__(76);

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
/* 69 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);
	var Backbone = __webpack_require__(45);
	var proxyCollection = __webpack_require__(70);
	var createFilter = __webpack_require__(71);

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
/* 70 */
/***/ function(module, exports, __webpack_require__) {

	
	var _ = __webpack_require__(2);
	var Backbone = __webpack_require__(45);

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
/* 71 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);

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
/* 72 */
/***/ function(module, exports, __webpack_require__) {

	
	var _ = __webpack_require__(2);
	var Backbone =__webpack_require__(45);
	var proxyCollection = __webpack_require__(70);
	var sortedIndex = __webpack_require__(73);

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
/* 73 */
/***/ function(module, exports, __webpack_require__) {

	
	var _ = __webpack_require__(2);

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
/* 74 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);
	var Backbone = __webpack_require__(45);
	var proxyCollection = __webpack_require__(70);

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
/* 75 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);

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
/* 76 */
/***/ function(module, exports, __webpack_require__) {

	/**
	 * query methods to extend Filtered Collection
	 */

	var _ = __webpack_require__(2);
	var query = __webpack_require__(77);

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
/* 77 */
/***/ function(module, exports, __webpack_require__) {

	var _ = __webpack_require__(2);
	var Parser = __webpack_require__(78);
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
/* 78 */
/***/ function(module, exports, __webpack_require__) {

	/* jshint -W071, -W074 */
	var _ = __webpack_require__(2);

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
/* 79 */
/***/ function(module, exports, __webpack_require__) {

	var DualCollection = __webpack_require__(55);
	var Model = __webpack_require__(80);
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);
	var bb = __webpack_require__(45);

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
/* 80 */
/***/ function(module, exports, __webpack_require__) {

	var DualModel = __webpack_require__(62);
	var Radio = __webpack_require__(41);
	var Utils = __webpack_require__(81);
	var debug = __webpack_require__(37)('order');
	var POS = __webpack_require__(36);
	var $ = __webpack_require__(40);

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
	  defaults: function(){
	    var customers = this.getEntities('customers'),
	        default_customer = customers['default'] || customers.guest || {};

	    return {
	      note          : '',
	      order_discount: 0,
	      customer_id   : default_customer.id,
	      customer      : default_customer
	    };
	  },

	  /**
	   * - attach tax settings
	   * - attach cart & gateways if order is open
	   */
	  initialize: function(){

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

	    this.gateways.fetch();
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
	POS.attach('Entities.Order.Model', Model);

/***/ },
/* 81 */
/***/ function(module, exports, __webpack_require__) {

	var accounting = __webpack_require__(48);
	var POS = __webpack_require__(36);
	var _ = __webpack_require__(2);
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

	module.exports = POS.Utils = Utils;

/***/ },
/* 82 */
/***/ function(module, exports, __webpack_require__) {

	var IndexedDBCollection = __webpack_require__(56);
	var Model = __webpack_require__(83);
	var _ = __webpack_require__(2);
	var bb = __webpack_require__(45);

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
/* 83 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(64);
	var debug = __webpack_require__(37)('cartItem');
	var Utils = __webpack_require__(81);
	var _ = __webpack_require__(2);
	var Radio = __webpack_require__(41);

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
/* 84 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(57);
	var Model = __webpack_require__(85);
	var Radio = __webpack_require__(41);

	module.exports = Collection.extend({
	  model: Model,
	  name: 'customers',

	  url: function(){
	    var wc_api = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'wc_api'
	    });
	    return wc_api + 'customers';
	  },

	  initialize: function(){
	    var settings = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'customers'
	    });
	    if(settings){
	      this._guest = settings.guest;
	      this._default = settings['default'] || settings.guest;
	    }
	  }
	});

/***/ },
/* 85 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(64);

	module.exports = Model.extend({
	  name: 'customer'
	});

/***/ },
/* 86 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(57);
	var Model = __webpack_require__(87);

	module.exports = Collection.extend({
	  model: Model
	});

/***/ },
/* 87 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(64);

	module.exports = Model.extend({});

/***/ },
/* 88 */
/***/ function(module, exports, __webpack_require__) {

	var DeepModel = __webpack_require__(63);
	var Radio = __webpack_require__(41);
	var polyglot = __webpack_require__(49);

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
/* 89 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(57);
	var Model = __webpack_require__(88);

	module.exports = Collection.extend({
	  model: Model
	});

/***/ },
/* 90 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(57);
	var Model = __webpack_require__(91);
	var $ = __webpack_require__(40);

	var gateways = [];
	$('.tmpl-checkout-gateways').each(function(){
	  gateways.push({
	    method_id    : $(this).data('gateway'),
	    method_title : $(this).data('title'),
	    icon         : $(this).data('icon'),
	    active       : (1 === $(this).data('default'))
	  });
	});

	module.exports = Collection.extend({
	  model: Model,

	  initialize: function() {
	    this._isNew = false;
	    this.on( 'change:active', this.onChangeActive );
	  },

	  fetch: function(){
	    this.add(gateways);
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
/* 91 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(64);

	module.exports = Model.extend({
	  idAttribute: 'method_id',
	  defaults: {
	    active: false
	  }
	});

/***/ },
/* 92 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(53);
	var Backbone = __webpack_require__(45);
	var LayoutView = __webpack_require__(93);
	var AlertView = __webpack_require__(101);
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);
	var globalChannel = __webpack_require__(41).channel('global');

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
/* 93 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(47);
	var Header = __webpack_require__(94);
	var _ = __webpack_require__(2);
	var $ = __webpack_require__(40);
	var hbs = __webpack_require__(96);
	var Tmpl = __webpack_require__(98);
	var Radio = __webpack_require__(41);
	var debug = __webpack_require__(37)('modalLayout');
	__webpack_require__(99);
	__webpack_require__(100);

	module.exports = LayoutView.extend({
	  template: hbs.compile(Tmpl),

	  //className: 'modal',
	  className: function(){
	    // if wp-admin, add css prefix
	    return window.adminpage ? 'wc_pos-modal' : 'modal';
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
/* 94 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var hbs = __webpack_require__(96);
	var Tmpl = __webpack_require__(97);
	var _ = __webpack_require__(2);
	var polyglot = __webpack_require__(49);

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
/* 95 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(44);
	var POS = __webpack_require__(36);

	module.exports = POS.ItemView = Mn.ItemView;

/***/ },
/* 96 */
/***/ function(module, exports) {

	module.exports = Handlebars;

/***/ },
/* 97 */
/***/ function(module, exports) {

	module.exports = "<h1>{{{title}}}</h1>\n<i class=\"{{iconPrefix}}-times\" data-action=\"close\" title=\"{{close}}\"></i>"

/***/ },
/* 98 */
/***/ function(module, exports) {

	module.exports = "<div class=\"modal-dialog\">\n  <div class=\"modal-content\">\n    <div class=\"modal-header\"></div>\n    <div class=\"modal-body\"></div>\n    <div class=\"modal-footer\"></div>\n  </div>\n</div>"

/***/ },
/* 99 */
/***/ function(module, exports) {

	/* ========================================================================
	 * Bootstrap: modal.js v3.3.4
	 * http://getbootstrap.com/javascript/#modals
	 * ========================================================================
	 * Copyright 2011-2015 Twitter, Inc.
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 * ======================================================================== */


	+function ($) {
	  'use strict';

	  // MODAL CLASS DEFINITION
	  // ======================

	  var Modal = function (element, options) {
	    this.options             = options
	    this.$body               = $(document.body)
	    this.$element            = $(element)
	    this.$dialog             = this.$element.find('.modal-dialog')
	    this.$backdrop           = null
	    this.isShown             = null
	    this.originalBodyPad     = null
	    this.scrollbarWidth      = 0
	    this.ignoreBackdropClick = false

	    if (this.options.remote) {
	      this.$element
	        .find('.modal-content')
	        .load(this.options.remote, $.proxy(function () {
	          this.$element.trigger('loaded.bs.modal')
	        }, this))
	    }
	  }

	  Modal.VERSION  = '3.3.4'

	  Modal.TRANSITION_DURATION = 300
	  Modal.BACKDROP_TRANSITION_DURATION = 150

	  Modal.DEFAULTS = {
	    backdrop: true,
	    keyboard: true,
	    show: true
	  }

	  Modal.prototype.toggle = function (_relatedTarget) {
	    return this.isShown ? this.hide() : this.show(_relatedTarget)
	  }

	  Modal.prototype.show = function (_relatedTarget) {
	    var that = this
	    var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

	    this.$element.trigger(e)

	    if (this.isShown || e.isDefaultPrevented()) return

	    this.isShown = true

	    this.checkScrollbar()
	    this.setScrollbar()
	    this.$body.addClass('modal-open')

	    this.escape()
	    this.resize()

	    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

	    this.$dialog.on('mousedown.dismiss.bs.modal', function () {
	      that.$element.one('mouseup.dismiss.bs.modal', function (e) {
	        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
	      })
	    })

	    this.backdrop(function () {
	      var transition = $.support.transition && that.$element.hasClass('fade')

	      if (!that.$element.parent().length) {
	        that.$element.appendTo(that.$body) // don't move modals dom position
	      }

	      that.$element
	        .show()
	        .scrollTop(0)

	      that.adjustDialog()

	      if (transition) {
	        that.$element[0].offsetWidth // force reflow
	      }

	      that.$element
	        .addClass('in')
	        .attr('aria-hidden', false)

	      that.enforceFocus()

	      var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

	      transition ?
	        that.$dialog // wait for modal to slide in
	          .one('bsTransitionEnd', function () {
	            that.$element.trigger('focus').trigger(e)
	          })
	          .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
	        that.$element.trigger('focus').trigger(e)
	    })
	  }

	  Modal.prototype.hide = function (e) {
	    if (e) e.preventDefault()

	    e = $.Event('hide.bs.modal')

	    this.$element.trigger(e)

	    if (!this.isShown || e.isDefaultPrevented()) return

	    this.isShown = false

	    this.escape()
	    this.resize()

	    $(document).off('focusin.bs.modal')

	    this.$element
	      .removeClass('in')
	      .attr('aria-hidden', true)
	      .off('click.dismiss.bs.modal')
	      .off('mouseup.dismiss.bs.modal')

	    this.$dialog.off('mousedown.dismiss.bs.modal')

	    $.support.transition && this.$element.hasClass('fade') ?
	      this.$element
	        .one('bsTransitionEnd', $.proxy(this.hideModal, this))
	        .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
	      this.hideModal()
	  }

	  Modal.prototype.enforceFocus = function () {
	    $(document)
	      .off('focusin.bs.modal') // guard against infinite focus loop
	      .on('focusin.bs.modal', $.proxy(function (e) {
	        if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
	          this.$element.trigger('focus')
	        }
	      }, this))
	  }

	  Modal.prototype.escape = function () {
	    if (this.isShown && this.options.keyboard) {
	      this.$element.on('keydown.dismiss.bs.modal', $.proxy(function (e) {
	        e.which == 27 && this.hide()
	      }, this))
	    } else if (!this.isShown) {
	      this.$element.off('keydown.dismiss.bs.modal')
	    }
	  }

	  Modal.prototype.resize = function () {
	    if (this.isShown) {
	      $(window).on('resize.bs.modal', $.proxy(this.handleUpdate, this))
	    } else {
	      $(window).off('resize.bs.modal')
	    }
	  }

	  Modal.prototype.hideModal = function () {
	    var that = this
	    this.$element.hide()
	    this.backdrop(function () {
	      that.$body.removeClass('modal-open')
	      that.resetAdjustments()
	      that.resetScrollbar()
	      that.$element.trigger('hidden.bs.modal')
	    })
	  }

	  Modal.prototype.removeBackdrop = function () {
	    this.$backdrop && this.$backdrop.remove()
	    this.$backdrop = null
	  }

	  Modal.prototype.backdrop = function (callback) {
	    var that = this
	    var animate = this.$element.hasClass('fade') ? 'fade' : ''

	    if (this.isShown && this.options.backdrop) {
	      var doAnimate = $.support.transition && animate

	      this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
	        .appendTo(this.$body)

	      this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
	        if (this.ignoreBackdropClick) {
	          this.ignoreBackdropClick = false
	          return
	        }
	        if (e.target !== e.currentTarget) return
	        this.options.backdrop == 'static'
	          ? this.$element[0].focus()
	          : this.hide()
	      }, this))

	      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

	      this.$backdrop.addClass('in')

	      if (!callback) return

	      doAnimate ?
	        this.$backdrop
	          .one('bsTransitionEnd', callback)
	          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
	        callback()

	    } else if (!this.isShown && this.$backdrop) {
	      this.$backdrop.removeClass('in')

	      var callbackRemove = function () {
	        that.removeBackdrop()
	        callback && callback()
	      }
	      $.support.transition && this.$element.hasClass('fade') ?
	        this.$backdrop
	          .one('bsTransitionEnd', callbackRemove)
	          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
	        callbackRemove()

	    } else if (callback) {
	      callback()
	    }
	  }

	  // these following methods are used to handle overflowing modals

	  Modal.prototype.handleUpdate = function () {
	    this.adjustDialog()
	  }

	  Modal.prototype.adjustDialog = function () {
	    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

	    this.$element.css({
	      paddingLeft:  !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
	      paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
	    })
	  }

	  Modal.prototype.resetAdjustments = function () {
	    this.$element.css({
	      paddingLeft: '',
	      paddingRight: ''
	    })
	  }

	  Modal.prototype.checkScrollbar = function () {
	    var fullWindowWidth = window.innerWidth
	    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
	      var documentElementRect = document.documentElement.getBoundingClientRect()
	      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
	    }
	    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
	    this.scrollbarWidth = this.measureScrollbar()
	  }

	  Modal.prototype.setScrollbar = function () {
	    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
	    this.originalBodyPad = document.body.style.paddingRight || ''
	    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
	  }

	  Modal.prototype.resetScrollbar = function () {
	    this.$body.css('padding-right', this.originalBodyPad)
	  }

	  Modal.prototype.measureScrollbar = function () { // thx walsh
	    var scrollDiv = document.createElement('div')
	    scrollDiv.className = 'modal-scrollbar-measure'
	    this.$body.append(scrollDiv)
	    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
	    this.$body[0].removeChild(scrollDiv)
	    return scrollbarWidth
	  }


	  // MODAL PLUGIN DEFINITION
	  // =======================

	  function Plugin(option, _relatedTarget) {
	    return this.each(function () {
	      var $this   = $(this)
	      var data    = $this.data('bs.modal')
	      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

	      if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
	      if (typeof option == 'string') data[option](_relatedTarget)
	      else if (options.show) data.show(_relatedTarget)
	    })
	  }

	  var old = $.fn.modal

	  $.fn.modal             = Plugin
	  $.fn.modal.Constructor = Modal


	  // MODAL NO CONFLICT
	  // =================

	  $.fn.modal.noConflict = function () {
	    $.fn.modal = old
	    return this
	  }


	  // MODAL DATA-API
	  // ==============

	  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
	    var $this   = $(this)
	    var href    = $this.attr('href')
	    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
	    var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

	    if ($this.is('a')) e.preventDefault()

	    $target.one('show.bs.modal', function (showEvent) {
	      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
	      $target.one('hidden.bs.modal', function () {
	        $this.is(':visible') && $this.trigger('focus')
	      })
	    })
	    Plugin.call($target, option, this)
	  })

	}(jQuery);


/***/ },
/* 100 */
/***/ function(module, exports) {

	/* ========================================================================
	 * Bootstrap: transition.js v3.3.4
	 * http://getbootstrap.com/javascript/#transitions
	 * ========================================================================
	 * Copyright 2011-2015 Twitter, Inc.
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 * ======================================================================== */


	+function ($) {
	  'use strict';

	  // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
	  // ============================================================

	  function transitionEnd() {
	    var el = document.createElement('bootstrap')

	    var transEndEventNames = {
	      WebkitTransition : 'webkitTransitionEnd',
	      MozTransition    : 'transitionend',
	      OTransition      : 'oTransitionEnd otransitionend',
	      transition       : 'transitionend'
	    }

	    for (var name in transEndEventNames) {
	      if (el.style[name] !== undefined) {
	        return { end: transEndEventNames[name] }
	      }
	    }

	    return false // explicit for ie8 (  ._.)
	  }

	  // http://blog.alexmaccaw.com/css-transitions
	  $.fn.emulateTransitionEnd = function (duration) {
	    var called = false
	    var $el = this
	    $(this).one('bsTransitionEnd', function () { called = true })
	    var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
	    setTimeout(callback, duration)
	    return this
	  }

	  $(function () {
	    $.support.transition = transitionEnd()

	    if (!$.support.transition) return

	    $.event.special.bsTransitionEnd = {
	      bindType: $.support.transition.end,
	      delegateType: $.support.transition.end,
	      handle: function (e) {
	        if ($(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
	      }
	    }
	  })

	}(jQuery);


/***/ },
/* 101 */
/***/ function(module, exports, __webpack_require__) {

	var View = __webpack_require__(95);
	var hbs = __webpack_require__(96);
	var Tmpl = __webpack_require__(102);

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
/* 102 */
/***/ function(module, exports) {

	module.exports = "<p>\n  {{message}}\n  {{#if raw}}\n    <a href=\"#\" data-action=\"raw\"><i class=\"icon icon-info-circle\"></i></a>\n  {{/if}}\n<p>\n{{#if raw}}\n  <div class=\"raw-output\" style=\"display:none\">{{{raw}}}</div>\n{{/if}}"

/***/ },
/* 103 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(53);
	var TabsView = __webpack_require__(104);
	var TabsCollection = __webpack_require__(108);
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
/* 104 */
/***/ function(module, exports, __webpack_require__) {

	var CollectionView = __webpack_require__(105);
	var Tab = __webpack_require__(106);

	var View = CollectionView.extend({
	  tagName: 'ul',
	  childView: Tab,
	  attributes: {
	    'role': 'tablist'
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
/* 105 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(44);
	var POS = __webpack_require__(36);

	module.exports = POS.CollectionView = Mn.CollectionView.extend({
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
/* 106 */
/***/ function(module, exports, __webpack_require__) {

	var hbs = __webpack_require__(96);
	var ItemView = __webpack_require__(95);
	var Tmpl = __webpack_require__(107);

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
/* 107 */
/***/ function(module, exports) {

	module.exports = "{{#unless fixed}}\n<a href=\"#\" data-action=\"remove\">\n  <i class=\"icon icon-times-circle\"></i>\n</a>\n{{/unless}}\n{{{ label }}}"

/***/ },
/* 108 */
/***/ function(module, exports, __webpack_require__) {

	var Collection = __webpack_require__(57);
	var Model = __webpack_require__(109);

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
	    if( activeTabs.length === 0 ) {
	      this.at(0).set({active: true});
	    }
	  }

	});

	module.exports = TabsCollection;

/***/ },
/* 109 */
/***/ function(module, exports, __webpack_require__) {

	var Model = __webpack_require__(64);

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
/* 110 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(53);
	var View = __webpack_require__(111);

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
/* 111 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var hbs = __webpack_require__(96);
	var _ = __webpack_require__(2);
	var tmpl = __webpack_require__(112);
	var polyglot = __webpack_require__(49);
	var ButtonsBehavior = __webpack_require__(113);

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
/* 112 */
/***/ function(module, exports) {

	module.exports = "{{#each buttons}}\n\n  {{#if this.button}}\n    <button class=\"btn {{this.className}}\"\n            {{#if this.action}}data-action=\"{{this.action}}\"{{/if}}\n      {{#if this.toggle}}data-toggle=\"{{this.toggle}}\"{{/if}}\n      {{#if this.loading}}data-loading=\"{{this.loadingText}}\"{{/if}}\n      {{#if this.icon}}data-icon=\"{{this.icon}}\"{{/if}}\n      {{#if this.disabled}}disabled{{/if}}\n      >\n      {{this.label}}\n    </button>\n  {{/if}}\n\n  {{#if this.link}}\n    <a href=\"#\" class=\"btn {{this.className}}\"\n       {{#if this.action}}data-action=\"{{this.action}}\"{{/if}}\n      {{#if this.toggle}}data-toggle=\"{{this.toggle}}\"{{/if}}\n      {{#if this.loading}}data-loading=\"{{this.loadingText}}\"{{/if}}\n      {{#if this.icon}}data-icon=\"{{this.icon}}\"{{/if}}\n      >\n      {{this.label}}\n    </a>\n  {{/if}}\n\n  {{#if this.input}}\n    <input type=\"button\" class=\"btn {{this.className}}\" value=\"{{this.label}}\"\n           {{#if this.action}}data-action=\"{{this.action}}\"{{/if}}\n      {{#if this.toggle}}data-toggle=\"{{this.toggle}}\"{{/if}}\n      {{#if this.loading}}data-loading=\"{{this.loadingText}}\"{{/if}}\n      >\n  {{/if}}\n\n  {{#if this.message}}\n    <p class=\"message {{this.className}}\"></p>\n  {{/if}}\n\n{{/each}}"

/***/ },
/* 113 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(114);
	var POS = __webpack_require__(36);
	var $ = __webpack_require__(40);
	var polyglot = __webpack_require__(49);
	var d = 'disabled';

	var Buttons = Behavior.extend({
	  loadingText: polyglot.t('messages.loading'),
	  iconPrefix: 'icon-',

	  initialize: function(){
	    // test for wp-admin
	    if(window.adminpage){
	      this.iconPrefix = 'wc_pos-icon-';
	    }
	  },

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
	    return '<i class="' + this.iconPrefix + state + '"></i>';
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
	      .addClass(state)
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
	POS.attach('Behaviors.Buttons', Buttons);

/***/ },
/* 114 */
/***/ function(module, exports, __webpack_require__) {

	var Mn = __webpack_require__(44);
	var POS = __webpack_require__(36);

	module.exports = POS.Behavior = Mn.Behavior;

/***/ },
/* 115 */
/***/ function(module, exports, __webpack_require__) {

	var POS = __webpack_require__(36);
	var Router = __webpack_require__(116);
	var LayoutView = __webpack_require__(120);
	var General = __webpack_require__(121);
	var Checkout = __webpack_require__(131);
	var HotKeys = __webpack_require__(135);
	var Access = __webpack_require__(137);
	var Tools = __webpack_require__(139);
	var Status = __webpack_require__(143);
	var bb = __webpack_require__(45);
	var Radio = bb.Radio;
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);

	var SettingsRouter = Router.extend({
	  initialize: function(options) {
	    this.container = options.container;
	    this.collection = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: 'settings'
	    });
	  },

	  onBeforeEnter: function() {
	    this.bootstrapTabs();
	    this.bootstrapSettings();
	    this.layout = new LayoutView();
	    this.listenTo(this.layout, 'show', this.showTabs);
	    this.container.show(this.layout);
	  },

	  bootstrapTabs: function(){
	    var tabs = [],
	        frag = bb.history.getFragment() || 'general';

	    // check page for templates
	    $('.tmpl-wc-pos-settings').each(function(){
	      tabs.push({
	        id    : $(this).data('id'),
	        label : $(this).data('label'),
	        active: ( $(this).data('id') === frag )
	      });
	    });

	    this.tabsArray = tabs;
	  },

	  bootstrapSettings: function(){
	    var settings = window.wc_pos_settings;

	    _.each(settings, function(setting, id){
	      var model = this.collection.add(setting);
	      model.set({ id: id });
	      model._isNew = false;
	    }, this);
	  },

	  routes: {
	    ''        : 'showGeneral',
	    'general' : 'showGeneral',
	    'checkout': 'showCheckout',
	    'hotkeys' : 'showHotkeys',
	    'access'  : 'showAccess',
	    'tools'   : 'showTools',
	    'status'  : 'showStatus'
	  },

	  onBeforeRoute: function() {
	    this.layout.getRegion('footer').empty();
	  },

	  showTabs: function(){

	    //var view = new Tabs({
	    //  collection: this.tabsArray
	    //});
	    var view = Radio.request('tabs', 'view', {
	      tabs: this.tabsArray
	    });

	    this.listenTo(view, 'show', function(){
	      // use wordpress admin styles
	      view.$el.addClass('nav-tab-wrapper');
	      view.children.each(function(child){
	        child.$el.addClass('nav-tab');
	      });
	    });

	    this.listenTo(view.collection, 'change:active', function(model, active){
	      if(active){
	        this.navigate(model.id, {
	          trigger: true,
	          replace: true
	        });
	      }
	    });

	    this.layout.getRegion('tabs').show(view);
	  },

	  showGeneral: function(){
	    var model = this.collection.get('general');
	    this.showFooter({model: model});
	    return new General({
	      container : this.layout.getRegion('settings'),
	      model: model
	    });
	  },

	  showCheckout: function(){
	    var model = this.collection.get('checkout');
	    this.showFooter({model: model});
	    return new Checkout({
	      container : this.layout.getRegion('settings'),
	      model: model
	    });
	  },

	  showHotkeys: function(){
	    var model = this.collection.get('hotkeys');
	    this.showFooter({model: model});
	    return new HotKeys({
	      container : this.layout.getRegion('settings'),
	      model: model
	    });
	  },

	  showAccess: function(){
	    var model = this.collection.get('access');
	    this.showFooter({model: model});
	    return new Access({
	      container : this.layout.getRegion('settings'),
	      model: model
	    });
	  },

	  showTools: function(){
	    return new Tools({
	      container : this.layout.getRegion('settings')
	    });
	  },

	  showStatus: function(){
	    return new Status({
	      container : this.layout.getRegion('settings')
	    });
	  },

	  showFooter: function(options){

	    _.defaults(options, {
	      buttons: [
	        {
	          action    : 'save',
	          className : 'button-primary',
	          icon      : 'append'
	        },{
	          type: 'message'
	        },{
	          action    : 'restore',
	          className : 'button-secondary alignright',
	          icon      : 'prepend'
	        }
	      ]
	    });

	    var view = Radio.request('buttons', 'view', options);

	    this.listenTo(view, {
	      'action:save': function(btn){
	        options.model.save([], { buttons: btn });
	      },
	      'action:restore': function(btn){
	        options.model.destroy({ buttons: btn });
	      }
	    });

	    this.layout.getRegion('footer').show(view);

	  }

	});

	module.exports = SettingsRouter;
	POS.attach('SettingsApp.Router', SettingsRouter);

/***/ },
/* 116 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(45);
	var Mn = __webpack_require__(44);
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);
	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);

	module.exports = POS.Router = Mn.AppRouter.extend({
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
/* 117 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(45);
	var Mn = __webpack_require__(44);
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);
	var POS = __webpack_require__(36);
	var LoadingService = __webpack_require__(118);
	var Radio = __webpack_require__(41);
	var globalChannel = Radio.channel('global');

	module.exports = POS.Route = Mn.Object.extend({
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
/* 118 */
/***/ function(module, exports, __webpack_require__) {

	var Service = __webpack_require__(53);
	var View = __webpack_require__(119);
	var _ = __webpack_require__(2);
	var debug = __webpack_require__(37)('loading');

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
/* 119 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var _ = __webpack_require__(2);
	var POS = __webpack_require__(36);

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
	POS.attach('Components.Loading.View', View);

/***/ },
/* 120 */
/***/ function(module, exports, __webpack_require__) {

	var LayoutView = __webpack_require__(47);
	var POS = __webpack_require__(36);

	var Layout = LayoutView.extend({

	  template: function(){
	    return '' +
	      '<div id="wc-pos-settings-tabs"></div>' +
	      '<div id="wc-pos-settings"></div>' +
	      '<div id="wc-pos-settings-footer"></div>';
	  },

	  regions: {
	    tabs    : '#wc-pos-settings-tabs',
	    settings: '#wc-pos-settings',
	    footer  : '#wc-pos-settings-footer'
	  }

	});

	module.exports = Layout;
	POS.attach('SettingsApp.LayoutView', Layout);

/***/ },
/* 121 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);
	var View = __webpack_require__(122);

	var General = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.model = options.model;
	  },

	  fetch: function() {
	    if(this.model.isNew()){
	      return this.model.fetch();
	    }
	  },

	  render: function() {
	    var view = new View({
	      model: this.model
	    });
	    this.container.show(view);
	  }

	});

	module.exports = General;
	POS.attach('SettingsApp.General.Route', General);

/***/ },
/* 122 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(123);
	var $ = __webpack_require__(40);
	var POS = __webpack_require__(36);
	var Select2 = __webpack_require__(127);
	var Tooltip = __webpack_require__(128);
	var CustomerSelect = __webpack_require__(130);

	var View = FormView.extend({

	  template: function(){
	    return $('script[data-id="general"]').html();
	  },

	  attributes: {
	    id: 'wc-pos-settings-general'
	  },

	  behaviors: {
	    Select2: {
	      behaviorClass: Select2,
	      maximumSelectionSize: 4
	    },
	    Tooltip: {
	      behaviorClass: Tooltip
	    }
	  },

	  modelEvents: {
	    'change:id': 'render'
	  },

	  onRender: function(){
	    var self = this;

	    // bind ordinary elements
	    this.$('input, select, textarea').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });

	    this.customerSelect();
	  },

	  customerSelect: function(){
	    var view = new CustomerSelect({
	      el    : this.$('*[data-component="customer-select"]'),
	      model : this.model
	    });
	    view.render();

	    // update model on customer select
	    this.listenTo(view, 'customer:select', function(customer) {
	      this.model.set({
	        default_customer: customer.id,
	        customer: customer
	      });
	    });

	    // disable customer select if logged_in_user checked
	    if(this.model.get('logged_in_user')){
	      view.triggerMethod('select:disable', true);
	    }

	    this.model.on('change:logged_in_user', function(model, toggle){
	      view.triggerMethod('select:disable', toggle);
	    });

	    // clean up
	    // TODO: abstract clean up
	    this.on('destroy', function(){
	      view.destroy();
	    });
	  }

	});

	module.exports = View;
	POS.attach('SettingsApp.General.View');

/***/ },
/* 123 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var bb = __webpack_require__(45);
	var POS = __webpack_require__(36);
	__webpack_require__(124);
	__webpack_require__(125);
	__webpack_require__(126);

	module.exports = POS.FormView = ItemView.extend({

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
	          .parent()
	          .removeClass('error');
	      },
	      invalid: function(view, attr) {
	        view
	          .$('input[name="' + attr + '"]')
	          .parent()
	          .addClass('error');
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
/* 124 */
/***/ function(module, exports, __webpack_require__) {

	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;// Backbone.Stickit v0.9.2, MIT Licensed
	// Copyright (c) 2012-2015 The New York Times, CMS Group, Matthew DeLambo <delambo@gmail.com>

	(function (factory) {

	  // Set up Stickit appropriately for the environment. Start with AMD.
	  if (true)
	    !(__WEBPACK_AMD_DEFINE_ARRAY__ = [__webpack_require__(2), __webpack_require__(45), exports], __WEBPACK_AMD_DEFINE_FACTORY__ = (factory), __WEBPACK_AMD_DEFINE_RESULT__ = (typeof __WEBPACK_AMD_DEFINE_FACTORY__ === 'function' ? (__WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__)) : __WEBPACK_AMD_DEFINE_FACTORY__), __WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));

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
/* 125 */
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
	    module.exports = factory(__webpack_require__(45), __webpack_require__(2));
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
/* 126 */
/***/ function(module, exports, __webpack_require__) {

	var bb = __webpack_require__(45);
	var _ = __webpack_require__(2);

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
	 * Multiple selects with Select2
	 * ... bit of a hack here due to strange model.set behavior with arrays
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
/* 127 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(114);
	var _ = __webpack_require__(2);
	var POS = __webpack_require__(36);

	var Select2 = Behavior.extend({

	  initialize: function(options){
	    options = options || {};
	    var defaults = {};
	    var methods = [
	      'query',
	      'initSelection',
	      'formatResult',
	      'formatSelection'
	    ];

	    _.each(methods, function(method){
	      if( this.view[method] ){
	        options[method] = _.bind(this.view[method], this.view);
	      }
	      defaults[method] = this[method];
	    }, this);

	    this.options = _.defaults(options, defaults);
	  },

	  ui: {
	    select: '.select2'
	  },

	  onRender: function() {
	    if(this.ui.select.hasClass('no-search')) {
	      this.options.dropdownCssClass = 'no-search';
	    }
	    this.ui.select.select2( this.options );
	  },

	  onBeforeDestroy: function() {
	    this.ui.select.select2( 'destroy' );
	  },

	  //query: function(){},
	  //initSelection: function(){},
	  //formatResult: function(){},
	  //formatSelection: function(){}

	  onSelectDisable: function(toggle){
	    this.ui.select.attr('disabled', toggle);
	  }

	});

	module.exports = Select2;
	POS.attach('Behaviors.Select2', Select2);

/***/ },
/* 128 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(114);
	var POS = __webpack_require__(36);
	__webpack_require__(129);

	var Tooltip = Behavior.extend({

	  initialize: function(options){
	    this.options = options;
	  },

	  ui: {
	    tooltip: '*[data-toggle="tooltip"]'
	  },

	  onRender: function() {
	    this.ui.tooltip.tooltip( this.options );
	  }

	});

	module.exports = Tooltip;
	POS.attach('Behaviors.Tooltip', Tooltip);

/***/ },
/* 129 */
/***/ function(module, exports) {

	/* ========================================================================
	 * Bootstrap: tooltip.js v3.3.4
	 * http://getbootstrap.com/javascript/#tooltip
	 * Inspired by the original jQuery.tipsy by Jason Frame
	 * ========================================================================
	 * Copyright 2011-2015 Twitter, Inc.
	 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
	 * ======================================================================== */


	+function ($) {
	  'use strict';

	  // TOOLTIP PUBLIC CLASS DEFINITION
	  // ===============================

	  var Tooltip = function (element, options) {
	    this.type       = null
	    this.options    = null
	    this.enabled    = null
	    this.timeout    = null
	    this.hoverState = null
	    this.$element   = null

	    this.init('tooltip', element, options)
	  }

	  Tooltip.VERSION  = '3.3.4'

	  Tooltip.TRANSITION_DURATION = 150

	  Tooltip.DEFAULTS = {
	    animation: true,
	    placement: 'top',
	    selector: false,
	    template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
	    trigger: 'hover focus',
	    title: '',
	    delay: 0,
	    html: false,
	    container: false,
	    viewport: {
	      selector: 'body',
	      padding: 0
	    }
	  }

	  Tooltip.prototype.init = function (type, element, options) {
	    this.enabled   = true
	    this.type      = type
	    this.$element  = $(element)
	    this.options   = this.getOptions(options)
	    this.$viewport = this.options.viewport && $(this.options.viewport.selector || this.options.viewport)

	    if (this.$element[0] instanceof document.constructor && !this.options.selector) {
	      throw new Error('`selector` option must be specified when initializing ' + this.type + ' on the window.document object!')
	    }

	    var triggers = this.options.trigger.split(' ')

	    for (var i = triggers.length; i--;) {
	      var trigger = triggers[i]

	      if (trigger == 'click') {
	        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
	      } else if (trigger != 'manual') {
	        var eventIn  = trigger == 'hover' ? 'mouseenter' : 'focusin'
	        var eventOut = trigger == 'hover' ? 'mouseleave' : 'focusout'

	        this.$element.on(eventIn  + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
	        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
	      }
	    }

	    this.options.selector ?
	      (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
	      this.fixTitle()
	  }

	  Tooltip.prototype.getDefaults = function () {
	    return Tooltip.DEFAULTS
	  }

	  Tooltip.prototype.getOptions = function (options) {
	    options = $.extend({}, this.getDefaults(), this.$element.data(), options)

	    if (options.delay && typeof options.delay == 'number') {
	      options.delay = {
	        show: options.delay,
	        hide: options.delay
	      }
	    }

	    return options
	  }

	  Tooltip.prototype.getDelegateOptions = function () {
	    var options  = {}
	    var defaults = this.getDefaults()

	    this._options && $.each(this._options, function (key, value) {
	      if (defaults[key] != value) options[key] = value
	    })

	    return options
	  }

	  Tooltip.prototype.enter = function (obj) {
	    var self = obj instanceof this.constructor ?
	      obj : $(obj.currentTarget).data('bs.' + this.type)

	    if (self && self.$tip && self.$tip.is(':visible')) {
	      self.hoverState = 'in'
	      return
	    }

	    if (!self) {
	      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
	      $(obj.currentTarget).data('bs.' + this.type, self)
	    }

	    clearTimeout(self.timeout)

	    self.hoverState = 'in'

	    if (!self.options.delay || !self.options.delay.show) return self.show()

	    self.timeout = setTimeout(function () {
	      if (self.hoverState == 'in') self.show()
	    }, self.options.delay.show)
	  }

	  Tooltip.prototype.leave = function (obj) {
	    var self = obj instanceof this.constructor ?
	      obj : $(obj.currentTarget).data('bs.' + this.type)

	    if (!self) {
	      self = new this.constructor(obj.currentTarget, this.getDelegateOptions())
	      $(obj.currentTarget).data('bs.' + this.type, self)
	    }

	    clearTimeout(self.timeout)

	    self.hoverState = 'out'

	    if (!self.options.delay || !self.options.delay.hide) return self.hide()

	    self.timeout = setTimeout(function () {
	      if (self.hoverState == 'out') self.hide()
	    }, self.options.delay.hide)
	  }

	  Tooltip.prototype.show = function () {
	    var e = $.Event('show.bs.' + this.type)

	    if (this.hasContent() && this.enabled) {
	      this.$element.trigger(e)

	      var inDom = $.contains(this.$element[0].ownerDocument.documentElement, this.$element[0])
	      if (e.isDefaultPrevented() || !inDom) return
	      var that = this

	      var $tip = this.tip()

	      var tipId = this.getUID(this.type)

	      this.setContent()
	      $tip.attr('id', tipId)
	      this.$element.attr('aria-describedby', tipId)

	      if (this.options.animation) $tip.addClass('fade')

	      var placement = typeof this.options.placement == 'function' ?
	        this.options.placement.call(this, $tip[0], this.$element[0]) :
	        this.options.placement

	      var autoToken = /\s?auto?\s?/i
	      var autoPlace = autoToken.test(placement)
	      if (autoPlace) placement = placement.replace(autoToken, '') || 'top'

	      $tip
	        .detach()
	        .css({ top: 0, left: 0, display: 'block' })
	        .addClass(placement)
	        .data('bs.' + this.type, this)

	      this.options.container ? $tip.appendTo(this.options.container) : $tip.insertAfter(this.$element)

	      var pos          = this.getPosition()
	      var actualWidth  = $tip[0].offsetWidth
	      var actualHeight = $tip[0].offsetHeight

	      if (autoPlace) {
	        var orgPlacement = placement
	        var $container   = this.options.container ? $(this.options.container) : this.$element.parent()
	        var containerDim = this.getPosition($container)

	        placement = placement == 'bottom' && pos.bottom + actualHeight > containerDim.bottom ? 'top'    :
	                    placement == 'top'    && pos.top    - actualHeight < containerDim.top    ? 'bottom' :
	                    placement == 'right'  && pos.right  + actualWidth  > containerDim.width  ? 'left'   :
	                    placement == 'left'   && pos.left   - actualWidth  < containerDim.left   ? 'right'  :
	                    placement

	        $tip
	          .removeClass(orgPlacement)
	          .addClass(placement)
	      }

	      var calculatedOffset = this.getCalculatedOffset(placement, pos, actualWidth, actualHeight)

	      this.applyPlacement(calculatedOffset, placement)

	      var complete = function () {
	        var prevHoverState = that.hoverState
	        that.$element.trigger('shown.bs.' + that.type)
	        that.hoverState = null

	        if (prevHoverState == 'out') that.leave(that)
	      }

	      $.support.transition && this.$tip.hasClass('fade') ?
	        $tip
	          .one('bsTransitionEnd', complete)
	          .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
	        complete()
	    }
	  }

	  Tooltip.prototype.applyPlacement = function (offset, placement) {
	    var $tip   = this.tip()
	    var width  = $tip[0].offsetWidth
	    var height = $tip[0].offsetHeight

	    // manually read margins because getBoundingClientRect includes difference
	    var marginTop = parseInt($tip.css('margin-top'), 10)
	    var marginLeft = parseInt($tip.css('margin-left'), 10)

	    // we must check for NaN for ie 8/9
	    if (isNaN(marginTop))  marginTop  = 0
	    if (isNaN(marginLeft)) marginLeft = 0

	    offset.top  = offset.top  + marginTop
	    offset.left = offset.left + marginLeft

	    // $.fn.offset doesn't round pixel values
	    // so we use setOffset directly with our own function B-0
	    $.offset.setOffset($tip[0], $.extend({
	      using: function (props) {
	        $tip.css({
	          top: Math.round(props.top),
	          left: Math.round(props.left)
	        })
	      }
	    }, offset), 0)

	    $tip.addClass('in')

	    // check to see if placing tip in new offset caused the tip to resize itself
	    var actualWidth  = $tip[0].offsetWidth
	    var actualHeight = $tip[0].offsetHeight

	    if (placement == 'top' && actualHeight != height) {
	      offset.top = offset.top + height - actualHeight
	    }

	    var delta = this.getViewportAdjustedDelta(placement, offset, actualWidth, actualHeight)

	    if (delta.left) offset.left += delta.left
	    else offset.top += delta.top

	    var isVertical          = /top|bottom/.test(placement)
	    var arrowDelta          = isVertical ? delta.left * 2 - width + actualWidth : delta.top * 2 - height + actualHeight
	    var arrowOffsetPosition = isVertical ? 'offsetWidth' : 'offsetHeight'

	    $tip.offset(offset)
	    this.replaceArrow(arrowDelta, $tip[0][arrowOffsetPosition], isVertical)
	  }

	  Tooltip.prototype.replaceArrow = function (delta, dimension, isVertical) {
	    this.arrow()
	      .css(isVertical ? 'left' : 'top', 50 * (1 - delta / dimension) + '%')
	      .css(isVertical ? 'top' : 'left', '')
	  }

	  Tooltip.prototype.setContent = function () {
	    var $tip  = this.tip()
	    var title = this.getTitle()

	    $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
	    $tip.removeClass('fade in top bottom left right')
	  }

	  Tooltip.prototype.hide = function (callback) {
	    var that = this
	    var $tip = $(this.$tip)
	    var e    = $.Event('hide.bs.' + this.type)

	    function complete() {
	      if (that.hoverState != 'in') $tip.detach()
	      that.$element
	        .removeAttr('aria-describedby')
	        .trigger('hidden.bs.' + that.type)
	      callback && callback()
	    }

	    this.$element.trigger(e)

	    if (e.isDefaultPrevented()) return

	    $tip.removeClass('in')

	    $.support.transition && $tip.hasClass('fade') ?
	      $tip
	        .one('bsTransitionEnd', complete)
	        .emulateTransitionEnd(Tooltip.TRANSITION_DURATION) :
	      complete()

	    this.hoverState = null

	    return this
	  }

	  Tooltip.prototype.fixTitle = function () {
	    var $e = this.$element
	    if ($e.attr('title') || typeof ($e.attr('data-original-title')) != 'string') {
	      $e.attr('data-original-title', $e.attr('title') || '').attr('title', '')
	    }
	  }

	  Tooltip.prototype.hasContent = function () {
	    return this.getTitle()
	  }

	  Tooltip.prototype.getPosition = function ($element) {
	    $element   = $element || this.$element

	    var el     = $element[0]
	    var isBody = el.tagName == 'BODY'

	    var elRect    = el.getBoundingClientRect()
	    if (elRect.width == null) {
	      // width and height are missing in IE8, so compute them manually; see https://github.com/twbs/bootstrap/issues/14093
	      elRect = $.extend({}, elRect, { width: elRect.right - elRect.left, height: elRect.bottom - elRect.top })
	    }
	    var elOffset  = isBody ? { top: 0, left: 0 } : $element.offset()
	    var scroll    = { scroll: isBody ? document.documentElement.scrollTop || document.body.scrollTop : $element.scrollTop() }
	    var outerDims = isBody ? { width: $(window).width(), height: $(window).height() } : null

	    return $.extend({}, elRect, scroll, outerDims, elOffset)
	  }

	  Tooltip.prototype.getCalculatedOffset = function (placement, pos, actualWidth, actualHeight) {
	    return placement == 'bottom' ? { top: pos.top + pos.height,   left: pos.left + pos.width / 2 - actualWidth / 2 } :
	           placement == 'top'    ? { top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2 } :
	           placement == 'left'   ? { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth } :
	        /* placement == 'right' */ { top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width }

	  }

	  Tooltip.prototype.getViewportAdjustedDelta = function (placement, pos, actualWidth, actualHeight) {
	    var delta = { top: 0, left: 0 }
	    if (!this.$viewport) return delta

	    var viewportPadding = this.options.viewport && this.options.viewport.padding || 0
	    var viewportDimensions = this.getPosition(this.$viewport)

	    if (/right|left/.test(placement)) {
	      var topEdgeOffset    = pos.top - viewportPadding - viewportDimensions.scroll
	      var bottomEdgeOffset = pos.top + viewportPadding - viewportDimensions.scroll + actualHeight
	      if (topEdgeOffset < viewportDimensions.top) { // top overflow
	        delta.top = viewportDimensions.top - topEdgeOffset
	      } else if (bottomEdgeOffset > viewportDimensions.top + viewportDimensions.height) { // bottom overflow
	        delta.top = viewportDimensions.top + viewportDimensions.height - bottomEdgeOffset
	      }
	    } else {
	      var leftEdgeOffset  = pos.left - viewportPadding
	      var rightEdgeOffset = pos.left + viewportPadding + actualWidth
	      if (leftEdgeOffset < viewportDimensions.left) { // left overflow
	        delta.left = viewportDimensions.left - leftEdgeOffset
	      } else if (rightEdgeOffset > viewportDimensions.width) { // right overflow
	        delta.left = viewportDimensions.left + viewportDimensions.width - rightEdgeOffset
	      }
	    }

	    return delta
	  }

	  Tooltip.prototype.getTitle = function () {
	    var title
	    var $e = this.$element
	    var o  = this.options

	    title = $e.attr('data-original-title')
	      || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

	    return title
	  }

	  Tooltip.prototype.getUID = function (prefix) {
	    do prefix += ~~(Math.random() * 1000000)
	    while (document.getElementById(prefix))
	    return prefix
	  }

	  Tooltip.prototype.tip = function () {
	    return (this.$tip = this.$tip || $(this.options.template))
	  }

	  Tooltip.prototype.arrow = function () {
	    return (this.$arrow = this.$arrow || this.tip().find('.tooltip-arrow'))
	  }

	  Tooltip.prototype.enable = function () {
	    this.enabled = true
	  }

	  Tooltip.prototype.disable = function () {
	    this.enabled = false
	  }

	  Tooltip.prototype.toggleEnabled = function () {
	    this.enabled = !this.enabled
	  }

	  Tooltip.prototype.toggle = function (e) {
	    var self = this
	    if (e) {
	      self = $(e.currentTarget).data('bs.' + this.type)
	      if (!self) {
	        self = new this.constructor(e.currentTarget, this.getDelegateOptions())
	        $(e.currentTarget).data('bs.' + this.type, self)
	      }
	    }

	    self.tip().hasClass('in') ? self.leave(self) : self.enter(self)
	  }

	  Tooltip.prototype.destroy = function () {
	    var that = this
	    clearTimeout(this.timeout)
	    this.hide(function () {
	      that.$element.off('.' + that.type).removeData('bs.' + that.type)
	    })
	  }


	  // TOOLTIP PLUGIN DEFINITION
	  // =========================

	  function Plugin(option) {
	    return this.each(function () {
	      var $this   = $(this)
	      var data    = $this.data('bs.tooltip')
	      var options = typeof option == 'object' && option

	      if (!data && /destroy|hide/.test(option)) return
	      if (!data) $this.data('bs.tooltip', (data = new Tooltip(this, options)))
	      if (typeof option == 'string') data[option]()
	    })
	  }

	  var old = $.fn.tooltip

	  $.fn.tooltip             = Plugin
	  $.fn.tooltip.Constructor = Tooltip


	  // TOOLTIP NO CONFLICT
	  // ===================

	  $.fn.tooltip.noConflict = function () {
	    $.fn.tooltip = old
	    return this
	  }

	}(jQuery);


/***/ },
/* 130 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var Select2 = __webpack_require__(127);
	var _ = __webpack_require__(2);
	var Radio = __webpack_require__(41);
	var hbs = __webpack_require__(96);
	var POS = __webpack_require__(36);
	//var debug = require('debug')('customerSelect');

	// Select view
	var View = ItemView.extend({

	  template: function(){
	    return '<input name="customer" type="hidden" class="select2">';
	  },

	  initialize: function(options){
	    options = options || {};
	    this.model = options.model;
	    this.customers = Radio.request('entities', 'get', {
	      type: 'collection',
	      name: 'customers'
	    });
	  },

	  behaviors: {
	    Select2: {
	      behaviorClass: Select2,
	      minimumInputLength: 3
	    }
	  },

	  ui: {
	    select: 'input[name="customer"]'
	  },

	  events: {
	    'change @ui.select' : 'onSelect',
	    'select2-opening @ui.select' : 'onSelectOpen'
	  },

	  /**
	   * using collection and Select2 query
	   * todo: when updating to v4 use Select2 ajax api, eg:
	   * ajax: {
	        url: "wc_api_url/customers",
	        dataType: 'json',
	        quietMillis: 250,
	        data: function (term, page) {
	            return {filter[q]: term};
	        },
	        results: function (data, page) {
	            return { results: data.customers };
	        },
	        cache: true
	    },
	   */
	  query: _.debounce(function(query){
	    var onSuccess = function(customers){
	      var results = customers.toJSON();
	      results.unshift(customers._guest);
	      query.callback({ results: results });
	    };
	    this.customers
	      .fetch({
	        // wp-admin requires auth
	        beforeSend: function(xhr){
	          xhr.setRequestHeader('X-WC-POS', 1);
	        },
	        data: 'filter[q]=' + query.term,
	        success: onSuccess
	      });
	  }, 250),

	  /**
	   *
	   */
	  initSelection: function( element, callback ) {
	    var customer;
	    if(this.model){ customer = this.model.get('customer'); }
	    if(!customer){ customer = this.customers._default; }
	    callback( customer );
	  },

	  /**
	   * select2 parse results
	   */
	  formatResult: function( customer ) {
	    var format = '{{first_name}} {{last_name}} ' +
	      '{{#if email}}({{email}}){{/if}}';

	    if( this.hasNoNames(customer) ){
	      format = '{{username}} ({{email}})';
	    }

	    var template = hbs.compile(format);
	    return template(customer);
	  },

	  /**
	   * select2 parse selection
	   */
	  formatSelection: function( customer ) {
	    var format = '{{first_name}} {{last_name}}';

	    if( this.hasNoNames(customer) ){
	      format = '{{username}}';
	    }

	    var template = hbs.compile(format);
	    return template(customer);
	  },

	  /**
	   *
	   */
	  hasNoNames: function(customer){
	    return _.chain(customer)
	      .pick('first_name', 'last_name')
	      .values()
	      .compact()
	      .isEmpty()
	      .value();
	  },

	  /**
	   *
	   */
	  onSelect: function(e) {
	    this.trigger( 'customer:select', e.added );
	  },

	  /**
	   *
	   */
	  onSelectOpen: function() {}

	});

	module.exports = View;
	POS.attach('Components.CustomerSelect.View', View);

/***/ },
/* 131 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);
	var View = __webpack_require__(132);
	var GatewaySettingsModal = __webpack_require__(134);
	var Radio = __webpack_require__(41);

	var SettingsRoute = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.model = options.model;
	  },

	  fetch: function() {
	    if(this.model.isNew()){
	      return this.model.fetch();
	    }
	  },

	  render: function() {
	    var view = new View({
	      model: this.model
	    });
	    this.listenTo(view, 'open:modal', this.openModal);
	    this.container.show(view);
	  },

	  openModal: function(id, view){
	    var model = this.model.collection.add({
	      id: 'gateway_' + id
	    });

	    if(!model.get('title')){
	      this.initModalData(model, view);
	    }

	    var modal = new GatewaySettingsModal({
	      tmpl: view.modalTmpl,
	      model: model
	    });

	    var self = this;
	    Radio.request('modal', 'open', modal)
	      .then(function(args){
	        var buttons = args.view.getButtons();
	        self.listenTo(buttons, 'action:save', function(btn){
	          model.save([], { buttons: btn });
	        });
	      });

	  },

	  initModalData: function(model, view){
	    function element(attr){
	      return '#' + model.id + ' .gateway-' + attr;
	    }
	    var data = {
	      title: view.$(element('name')).html(),
	      description: view.$(element('description')).html(),
	      icon: view.$(element('icon')).data('show') ? true : false
	    };
	    data.hasIcon = view.$(element('icon')).data('icon');
	    model.set(data);
	  }

	});

	module.exports = SettingsRoute;
	POS.attach('SettingsApp.Route', SettingsRoute);

/***/ },
/* 132 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(123);
	var $ = __webpack_require__(40);
	var POS = __webpack_require__(36);
	var Tooltip = __webpack_require__(128);
	var Sortable = __webpack_require__(133);

	var View = FormView.extend({
	  template: function(){
	    return $('script[data-id="checkout"]').html();
	  },

	  attributes: {
	    id: 'wc-pos-settings-checkout'
	  },

	  modelEvents: {
	    'change:id': 'render'
	  },

	  onRender: function(){
	    var self = this;
	    this.$('input, select, textarea').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });
	    this.modalTmpl = this.$('#tmpl-gateway-settings-modal').html();
	  },

	  ui: {
	    settings: '.gateway-settings'
	  },

	  events: {
	    'click @ui.settings': 'openGatewaySettingsModal'
	  },

	  behaviors: {
	    Tooltip: {
	      behaviorClass: Tooltip
	    },
	    Sortable: {
	      behaviorClass: Sortable
	    }
	  },

	  openGatewaySettingsModal: function(e){
	    e.preventDefault();
	    var gateway = $(e.target).data('gateway');
	    this.trigger('open:modal', gateway, this);
	  }

	});

	module.exports = View;
	POS.attach('SettingsApp.View');

/***/ },
/* 133 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(114);
	var POS = __webpack_require__(36);
	var $ = __webpack_require__(40);
	var _ = __webpack_require__(2);

	var Sortable = Behavior.extend({

	  initialize: function(options){

	    this.options = _.defaults(options, {
	      items:'tr',
	      cursor:'move',
	      axis:'y',
	      handle: 'td',
	      scrollSensitivity:40,
	      helper:function(e,ui){
	        ui.children().each(function(){
	          $(this).width($(this).width());
	        });
	        return ui;
	      },
	      start:function(event,ui){
	        ui.item.css('background-color','#f6f6f6');
	      },
	      stop:function(event,ui){
	        ui.item.removeAttr('style');
	        $('input.gateway_order', this).each(function(idx) {
	          $(this).val(idx);
	          $(this).trigger('input');
	        });
	      }
	    });

	  },

	  ui: {
	    sortable: '.sortable'
	  },

	  onRender: function() {
	    if( this.ui.sortable.length > 0 ) {

	      // Custom sorting for checkout settings table
	      // TODO: move this to view callback
	      var table = this.ui.sortable;
	      var rows = table.find('tbody tr').get();

	      // sort according to input
	      table.append(rows.sort(function(a, b) {
	        return parseInt($(a).find('input.gateway_order').val(), 10) -
	          parseInt($(b).find('input.gateway_order').val(), 10);
	      }));

	      table.sortable( this.options );
	    }

	  }

	});

	module.exports = Sortable;
	POS.attach('Behaviors.Sortable', Sortable);

/***/ },
/* 134 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(123);
	var $ = __webpack_require__(40);
	var Tooltip = __webpack_require__(128);
	var Radio = __webpack_require__(41);

	module.exports = FormView.extend({
	  tagName: 'table',
	  className: 'form-table',

	  initialize: function (options) {
	    options = options || {};
	    this.template = options.tmpl.trim();
	    // modal setup
	    this.modal = {
	      header: {
	        title: this.model.get('title')
	      },
	      footer: {
	        buttons: [
	          {
	            type: 'message'
	          },{
	            action    : 'save',
	            className : 'button-primary',
	            icon      : 'prepend'
	          }
	        ]
	      }
	    };
	  },

	  behaviors: {
	    Tooltip: {
	      behaviorClass: Tooltip
	    }
	  },

	  modelEvents: {
	    'change:title': function(modal, value){
	      var update = {};
	      update.header = { title: value };
	      Radio.request('modal', 'update', update);
	    }
	  },

	  onRender: function(){
	    var self = this;
	    this.$('input, select, textarea').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });

	    if(this.model.get('hasIcon')){
	      this.$('#icon').closest('tr').show();
	    }
	  }

	});

/***/ },
/* 135 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);
	var View = __webpack_require__(136);

	var HotKeys = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.model = options.model;
	  },

	  render: function() {
	    var view = new View({
	      model: this.model
	    });
	    this.container.show(view);
	  }

	});

	module.exports = HotKeys;
	POS.attach('SettingsApp.HotKeys.Route', HotKeys);

/***/ },
/* 136 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(123);
	var $ = __webpack_require__(40);
	var POS = __webpack_require__(36);
	var Tooltip = __webpack_require__(128);

	var View = FormView.extend({

	  template: function(){
	    return $('script[data-id="hotkeys"]').html();
	  },

	  attributes: {
	    id: 'wc-pos-settings-hotkeys'
	  },

	  behaviors: {
	    Tooltip: {
	      behaviorClass: Tooltip
	    }
	  },

	  modelEvents: {
	    'change:id': 'render'
	  },

	  onRender: function(){
	    var self = this;

	    // bind ordinary elements
	    this.$('input, select, textarea').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });

	  }

	});

	module.exports = View;
	POS.attach('SettingsApp.HotKeys.View');

/***/ },
/* 137 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);
	var View = __webpack_require__(138);

	var Access = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	    this.model = options.model;
	  },

	  fetch: function() {
	    if(this.model.isNew()){
	      return this.model.fetch();
	    }
	  },

	  render: function() {
	    var view = new View({
	      model: this.model
	    });
	    this.container.show(view);
	  }

	});

	module.exports = Access;
	POS.attach('SettingsApp.Access.Route', Access);

/***/ },
/* 138 */
/***/ function(module, exports, __webpack_require__) {

	var FormView = __webpack_require__(123);
	var POS = __webpack_require__(36);
	var $ = __webpack_require__(40);

	var View = FormView.extend({

	  template: function(){
	    return $('script[data-id="access"]').html();
	  },

	  attributes: {
	    id: 'wc-pos-settings-access'
	  },

	  ui: {
	    tabs    : '.wc-pos-access-tabs > li',
	    options : '.wc-pos-access-panel > li'
	  },

	  events: {
	    'click @ui.tabs' : 'onTabClick'
	  },

	  modelEvents: {
	    'change:id': 'render'
	  },

	  onRender: function(){
	    var self = this;

	    // bind ordinary elements
	    this.$('input, select, textarea').each(function(){
	      var name = $(this).attr('name');
	      if(name){
	        self.addBinding(null, '*[name="' + name + '"]', name);
	      }
	    });

	    // init the first tab
	    this.ui.tabs.first().addClass('active');
	    this.ui.options.first().addClass('active');
	  },

	  onTabClick: function(e){
	    this.ui.tabs.each(function(){
	      $(this).removeClass('active');
	    });
	    this.ui.options.each(function(){
	      $(this).removeClass('active');
	    });
	    $(e.currentTarget).addClass('active');
	    var option = $(e.currentTarget).data('id');
	    $('#' + option).addClass('active');
	  }

	});

	module.exports = View;
	POS.attach('SettingsApp.Access.View');

/***/ },
/* 139 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);
	var View = __webpack_require__(140);
	var TranslationModal = __webpack_require__(142);
	var Radio = __webpack_require__(41);

	var Tools = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;
	  },

	  render: function() {
	    var view = new View();
	    this.listenTo(view, {
	      'translation:update': this.openTranslationModal
	    });
	    this.container.show(view);
	  },

	  openTranslationModal: function(args){
	    var title = args.view
	      .$('[data-action="translation"]')
	      .data('title');

	    var view = new TranslationModal({
	      title: title
	    });
	    Radio.request('modal', 'open', view);
	  }

	});

	module.exports = Tools;
	POS.attach('SettingsApp.Tools.Route', Tools);

/***/ },
/* 140 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var POS = __webpack_require__(36);
	var EmulateHTTP = __webpack_require__(141);
	var $ = __webpack_require__(40);

	var View = ItemView.extend({

	  template: function(){
	    return $('script[data-id="tools"]').html();
	  },

	  attributes: {
	    id: 'wc-pos-settings-tools'
	  },

	  behaviors: {
	    EmulateHTTP: {
	      behaviorClass: EmulateHTTP
	    }
	  },

	  ui: {
	    translation: '*[data-action="translation"]'
	  },

	  triggers: {
	    'click @ui.translation': 'translation:update'
	  }

	});

	module.exports = View;
	POS.attach('SettingsApp.Tools.View');

/***/ },
/* 141 */
/***/ function(module, exports, __webpack_require__) {

	var Behavior = __webpack_require__(114);
	var POS = __webpack_require__(36);
	var $ = __webpack_require__(40);
	var Radio = __webpack_require__(41);

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
	POS.attach('Behaviors.EmulateHTTP', EmulateHTTP);

/***/ },
/* 142 */
/***/ function(module, exports, __webpack_require__) {

	/* WEBPACK VAR INJECTION */(function(global) {var ItemView = __webpack_require__(95);
	var Radio = __webpack_require__(41);
	var EventSource = global['EventSource'];

	module.exports =  ItemView.extend({
	  template: function(){
	    return '<i class="spinner"></i>';
	  },

	  initialize: function (options) {
	    options = options || {};

	    this.modal = {
	      header: {
	        title: options.title
	      },
	      footer: {
	        show: false,
	        buttons: [{
	          action: 'close',
	          className: 'button'
	        }]
	      }
	    };
	  },

	  onShow: function() {
	    var view = this,
	        url = this.constructURL(),
	        stream = new EventSource(url);

	    stream.onmessage = function(e){
	      if( e.data === 'complete' ){
	        this.close();
	        view.$('.spinner').hide();
	        Radio.request('modal', 'update', { footer: {
	          show: true
	        }});
	      } else {
	        view.$('.spinner').before('<p>' + e.data + '</p>');
	      }
	    };
	  },

	  constructURL: function(){
	    var ajaxurl = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'ajaxurl'
	    });
	    var nonce = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'nonce'
	    });

	    return ajaxurl + '?action=wc_pos_update_translations&security=' + nonce;
	  }
	});
	/* WEBPACK VAR INJECTION */}.call(exports, (function() { return this; }())))

/***/ },
/* 143 */
/***/ function(module, exports, __webpack_require__) {

	var Route = __webpack_require__(117);
	var POS = __webpack_require__(36);
	var View = __webpack_require__(144);
	var Radio = __webpack_require__(41);
	var $ = __webpack_require__(40);

	var Status = Route.extend({

	  initialize: function( options ) {
	    options = options || {};
	    this.container = options.container;

	    this.ajaxurl = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'ajaxurl'
	    });

	    this.nonce = Radio.request('entities', 'get', {
	      type: 'option',
	      name: 'nonce'
	    });
	  },

	  fetch: function(){
	    var self = this;
	    return $.getJSON( this.ajaxurl, {
	      action: 'wc_pos_system_status',
	      security: this.nonce
	    }, function( resp ){
	      self.tests = resp;
	    });
	  },

	  render: function() {
	    var view = new View({
	      tests: this.tests
	    });
	    this.container.show(view);
	  }

	});

	module.exports = Status;
	POS.attach('SettingsApp.Status.Route', Status);

/***/ },
/* 144 */
/***/ function(module, exports, __webpack_require__) {

	var ItemView = __webpack_require__(95);
	var POS = __webpack_require__(36);
	var EmulateHTTP = __webpack_require__(141);
	var $ = __webpack_require__(40);
	var hbs = __webpack_require__(96);

	var View = ItemView.extend({

	  template: hbs.compile( $('script[data-id="status"]').html() ),

	  attributes: {
	    id: 'wc-pos-settings-status'
	  },

	  behaviors: {
	    EmulateHTTP: {
	      behaviorClass: EmulateHTTP
	    }
	  },

	  templateHelpers: function(){
	    return this.options.tests;
	  }

	});

	module.exports = View;
	POS.attach('SettingsApp.Status.View');

/***/ }
/******/ ]);