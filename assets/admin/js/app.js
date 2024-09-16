/*! AdminLTE app.js
 * ================
 * Main JS application file for AdminLTE v2. This file
 * should be included in all pages. It controls some layout
 * options and implements exclusive AdminLTE plugins.
 *
 * @Author  Almsaeed Studio
 * @Support <http://www.almsaeedstudio.com>
 * @Email   <support@almsaeedstudio.com>
 * @version 2.3.0
 * @license MIT <http://opensource.org/licenses/MIT>
 */
(function (window, undefined) {
    "use strict";
    var
        console = window.console || undefined,
        document = window.document,
        navigator = window.navigator,
        sessionStorage = false,
        setTimeout = window.setTimeout,
        clearTimeout = window.clearTimeout,
        setInterval = window.setInterval,
        clearInterval = window.clearInterval,
        JSON = window.JSON,
        alert = window.alert,
        History = window.History = window.History || {},
        history = window.history;
    try {
        sessionStorage = window.sessionStorage;
        sessionStorage.setItem('TEST', '1');
        sessionStorage.removeItem('TEST');
    } catch (e) {
        sessionStorage = false;
    }
    JSON.stringify = JSON.stringify || JSON.encode;
    JSON.parse = JSON.parse || JSON.decode;
    if (typeof History.init !== 'undefined') {
        throw new Error('History.js Core has already been loaded...');
    }
    History.init = function (options) {
        if (typeof History.Adapter === 'undefined') {
            return false;
        }
        if (typeof History.initCore !== 'undefined') {
            History.initCore();
        }
        if (typeof History.initHtml4 !== 'undefined') {
            History.initHtml4();
        }
        return true;
    };
    History.initCore = function (options) {
        if (typeof History.initCore.initialized !== 'undefined') {
            return false;
        } else {
            History.initCore.initialized = true;
        }
        History.options = History.options || {};
        History.options.hashChangeInterval = History.options.hashChangeInterval || 100;
        History.options.safariPollInterval = History.options.safariPollInterval || 500;
        History.options.doubleCheckInterval = History.options.doubleCheckInterval || 500;
        History.options.disableSuid = History.options.disableSuid || false;
        History.options.storeInterval = History.options.storeInterval || 1000;
        History.options.busyDelay = History.options.busyDelay || 250;
        History.options.debug = History.options.debug || false;
        History.options.initialTitle = History.options.initialTitle || document.title;
        History.options.html4Mode = History.options.html4Mode || false;
        History.options.delayInit = History.options.delayInit || false;
        History.intervalList = [];
        History.clearAllIntervals = function () {
            var i, il = History.intervalList;
            if (typeof il !== "undefined" && il !== null) {
                for (i = 0; i < il.length; i++) {
                    clearInterval(il[i]);
                }
                History.intervalList = null;
            }
        };
        History.debug = function () {
            if ((History.options.debug || false)) {
                History.log.apply(History, arguments);
            }
        };
        History.log = function () {
            var
                consoleExists = !(typeof console === 'undefined' || typeof console.log === 'undefined' || typeof console.log.apply === 'undefined'),
                textarea = document.getElementById('log'),
                message, i, n, args, arg;
            if (consoleExists) {
                args = Array.prototype.slice.call(arguments);
                message = args.shift();
                if (typeof console.debug !== 'undefined') {
                    console.debug.apply(console, [message, args]);
                } else {
                    console.log.apply(console, [message, args]);
                }
            } else {
                message = ("\n" + arguments[0] + "\n");
            }
            for (i = 1, n = arguments.length; i < n; ++i) {
                arg = arguments[i];
                if (typeof arg === 'object' && typeof JSON !== 'undefined') {
                    try {
                        arg = JSON.stringify(arg);
                    } catch (Exception) {
                    }
                }
                message += "\n" + arg + "\n";
            }
            if (textarea) {
                textarea.value += message + "\n-----\n";
                textarea.scrollTop = textarea.scrollHeight - textarea.clientHeight;
            } else if (!consoleExists) {
                alert(message);
            }
            return true;
        };
        History.getInternetExplorerMajorVersion = function () {
            var result = History.getInternetExplorerMajorVersion.cached = (typeof History.getInternetExplorerMajorVersion.cached !== 'undefined') ? History.getInternetExplorerMajorVersion.cached : (function () {
                var v = 3,
                    div = document.createElement('div'),
                    all = div.getElementsByTagName('i');
                while ((div.innerHTML = '<!--[if gt IE ' + (++v) + ']><i></i><![endif]-->') && all[0]) {
                }
                return (v > 4) ? v : false;
            })();
            return result;
        };
        History.isInternetExplorer = function () {
            var result = History.isInternetExplorer.cached = (typeof History.isInternetExplorer.cached !== 'undefined') ? History.isInternetExplorer.cached : Boolean(History.getInternetExplorerMajorVersion());
            return result;
        };
        if (History.options.html4Mode) {
            History.emulated = {
                pushState: true,
                hashChange: true
            };
        } else {
            History.emulated = {
                pushState: !Boolean(window.history && window.history.pushState && window.history.replaceState && !((/ Mobile\/([1-7][a-z]|(8([abcde]|f(1[0-8]))))/i).test(navigator.userAgent) || (/AppleWebKit\/5([0-2]|3[0-2])/i).test(navigator.userAgent))),
                hashChange: Boolean(!(('onhashchange' in window) || ('onhashchange' in document)) || (History.isInternetExplorer() && History.getInternetExplorerMajorVersion() < 8))
            };
        }
        History.enabled = !History.emulated.pushState;
        History.bugs = {
            setHash: Boolean(!History.emulated.pushState && navigator.vendor === 'Apple Computer, Inc.' && /AppleWebKit\/5([0-2]|3[0-3])/.test(navigator.userAgent)),
            safariPoll: Boolean(!History.emulated.pushState && navigator.vendor === 'Apple Computer, Inc.' && /AppleWebKit\/5([0-2]|3[0-3])/.test(navigator.userAgent)),
            ieDoubleCheck: Boolean(History.isInternetExplorer() && History.getInternetExplorerMajorVersion() < 8),
            hashEscape: Boolean(History.isInternetExplorer() && History.getInternetExplorerMajorVersion() < 7)
        };
        History.isEmptyObject = function (obj) {
            for (var name in obj) {
                if (obj.hasOwnProperty(name)) {
                    return false;
                }
            }
            return true;
        };
        History.cloneObject = function (obj) {
            var hash, newObj;
            if (obj) {
                hash = JSON.stringify(obj);
                newObj = JSON.parse(hash);
            } else {
                newObj = {};
            }
            return newObj;
        };
        History.getRootUrl = function () {
            var rootUrl = document.location.protocol + '//' + (document.location.hostname || document.location.host);
            if (document.location.port || false) {
                rootUrl += ':' + document.location.port;
            }
            rootUrl += '/';
            return rootUrl;
        };
        History.getBaseHref = function () {
            var
                baseElements = document.getElementsByTagName('base'),
                baseElement = null,
                baseHref = '';
            if (baseElements.length === 1) {
                baseElement = baseElements[0];
                baseHref = baseElement.href.replace(/[^\/]+$/, '');
            }
            baseHref = baseHref.replace(/\/+$/, '');
            if (baseHref) baseHref += '/';
            return baseHref;
        };
        History.getBaseUrl = function () {
            var baseUrl = History.getBaseHref() || History.getBasePageUrl() || History.getRootUrl();
            return baseUrl;
        };
        History.getPageUrl = function () {
            var
                State = History.getState(false, false),
                stateUrl = (State || {}).url || History.getLocationHref(),
                pageUrl;
            pageUrl = stateUrl.replace(/\/+$/, '').replace(/[^\/]+$/, function (part, index, string) {
                return (/\./).test(part) ? part : part + '/';
            });
            return pageUrl;
        };
        History.getBasePageUrl = function () {
            var basePageUrl = (History.getLocationHref()).replace(/[#\?].*/, '').replace(/[^\/]+$/, function (part, index, string) {
                    return (/[^\/]$/).test(part) ? '' : part;
                }).replace(/\/+$/, '') + '/';
            return basePageUrl;
        };
        History.getFullUrl = function (url, allowBaseHref) {
            var fullUrl = url,
                firstChar = url.substring(0, 1);
            allowBaseHref = (typeof allowBaseHref === 'undefined') ? true : allowBaseHref;
            if (/[a-z]+\:\/\//.test(url)) {
            } else if (firstChar === '/') {
                fullUrl = History.getRootUrl() + url.replace(/^\/+/, '');
            } else if (firstChar === '#') {
                fullUrl = History.getPageUrl().replace(/#.*/, '') + url;
            } else if (firstChar === '?') {
                fullUrl = History.getPageUrl().replace(/[\?#].*/, '') + url;
            } else {
                if (allowBaseHref) {
                    fullUrl = History.getBaseUrl() + url.replace(/^(\.\/)+/, '');
                } else {
                    fullUrl = History.getBasePageUrl() + url.replace(/^(\.\/)+/, '');
                }
            }
            return fullUrl.replace(/\#$/, '');
        };
        History.getShortUrl = function (url) {
            var shortUrl = url,
                baseUrl = History.getBaseUrl(),
                rootUrl = History.getRootUrl();
            if (History.emulated.pushState) {
                shortUrl = shortUrl.replace(baseUrl, '');
            }
            shortUrl = shortUrl.replace(rootUrl, '/');
            if (History.isTraditionalAnchor(shortUrl)) {
                shortUrl = './' + shortUrl;
            }
            shortUrl = shortUrl.replace(/^(\.\/)+/g, './').replace(/\#$/, '');
            return shortUrl;
        };
        History.getLocationHref = function (doc) {
            doc = doc || document;
            if (doc.URL === doc.location.href)
                return doc.location.href;
            if (doc.location.href === decodeURIComponent(doc.URL))
                return doc.URL;
            if (doc.location.hash && decodeURIComponent(doc.location.href.replace(/^[^#]+/, "")) === doc.location.hash)
                return doc.location.href;
            if (doc.URL.indexOf('#') == -1 && doc.location.href.indexOf('#') != -1)
                return doc.location.href;
            return doc.URL || doc.location.href;
        };
        History.store = {};
        History.idToState = History.idToState || {};
        History.stateToId = History.stateToId || {};
        History.urlToId = History.urlToId || {};
        History.storedStates = History.storedStates || [];
        History.savedStates = History.savedStates || [];
        History.normalizeStore = function () {
            History.store.idToState = History.store.idToState || {};
            History.store.urlToId = History.store.urlToId || {};
            History.store.stateToId = History.store.stateToId || {};
        };
        History.getState = function (friendly, create) {
            if (typeof friendly === 'undefined') {
                friendly = true;
            }
            if (typeof create === 'undefined') {
                create = true;
            }
            var State = History.getLastSavedState();
            if (!State && create) {
                State = History.createStateObject();
            }
            if (friendly) {
                State = History.cloneObject(State);
                State.url = State.cleanUrl || State.url;
            }
            return State;
        };
        History.getIdByState = function (newState) {
            var id = History.extractId(newState.url),
                str;
            if (!id) {
                str = History.getStateString(newState);
                if (typeof History.stateToId[str] !== 'undefined') {
                    id = History.stateToId[str];
                } else if (typeof History.store.stateToId[str] !== 'undefined') {
                    id = History.store.stateToId[str];
                } else {
                    while (true) {
                        id = (new Date()).getTime() + String(Math.random()).replace(/\D/g, '');
                        if (typeof History.idToState[id] === 'undefined' && typeof History.store.idToState[id] === 'undefined') {
                            break;
                        }
                    }
                    History.stateToId[str] = id;
                    History.idToState[id] = newState;
                }
            }
            return id;
        };
        History.normalizeState = function (oldState) {
            var newState, dataNotEmpty;
            if (!oldState || (typeof oldState !== 'object')) {
                oldState = {};
            }
            if (typeof oldState.normalized !== 'undefined') {
                return oldState;
            }
            if (!oldState.data || (typeof oldState.data !== 'object')) {
                oldState.data = {};
            }
            newState = {};
            newState.normalized = true;
            newState.title = oldState.title || '';
            newState.url = History.getFullUrl(oldState.url ? oldState.url : (History.getLocationHref()));
            newState.hash = History.getShortUrl(newState.url);
            newState.data = History.cloneObject(oldState.data);
            newState.id = History.getIdByState(newState);
            newState.cleanUrl = newState.url.replace(/\??\&_suid.*/, '');
            newState.url = newState.cleanUrl;
            dataNotEmpty = !History.isEmptyObject(newState.data);
            if ((newState.title || dataNotEmpty) && History.options.disableSuid !== true) {
                newState.hash = History.getShortUrl(newState.url).replace(/\??\&_suid.*/, '');
                if (!/\?/.test(newState.hash)) {
                    newState.hash += '?';
                }
                newState.hash += '&_suid=' + newState.id;
            }
            newState.hashedUrl = History.getFullUrl(newState.hash);
            if ((History.emulated.pushState || History.bugs.safariPoll) && History.hasUrlDuplicate(newState)) {
                newState.url = newState.hashedUrl;
            }
            return newState;
        };
        History.createStateObject = function (data, title, url) {
            var State = {
                'data': data,
                'title': title,
                'url': url
            };
            State = History.normalizeState(State);
            return State;
        };
        History.getStateById = function (id) {
            id = String(id);
            var State = History.idToState[id] || History.store.idToState[id] || undefined;
            return State;
        };
        History.getStateString = function (passedState) {
            var State, cleanedState, str;
            State = History.normalizeState(passedState);
            cleanedState = {
                data: State.data,
                title: passedState.title,
                url: passedState.url
            };
            str = JSON.stringify(cleanedState);
            return str;
        };
        History.getStateId = function (passedState) {
            var State, id;
            State = History.normalizeState(passedState);
            id = State.id;
            return id;
        };
        History.getHashByState = function (passedState) {
            var State, hash;
            State = History.normalizeState(passedState);
            hash = State.hash;
            return hash;
        };
        History.extractId = function (url_or_hash) {
            var id, parts, url, tmp;
            if (url_or_hash.indexOf('#') != -1) {
                tmp = url_or_hash.split("#")[0];
            } else {
                tmp = url_or_hash;
            }
            parts = /(.*)\&_suid=([0-9]+)$/.exec(tmp);
            url = parts ? (parts[1] || url_or_hash) : url_or_hash;
            id = parts ? String(parts[2] || '') : '';
            return id || false;
        };
        History.isTraditionalAnchor = function (url_or_hash) {
            var isTraditional = !(/[\/\?\.]/.test(url_or_hash));
            return isTraditional;
        };
        History.extractState = function (url_or_hash, create) {
            var State = null,
                id, url;
            create = create || false;
            id = History.extractId(url_or_hash);
            if (id) {
                State = History.getStateById(id);
            }
            if (!State) {
                url = History.getFullUrl(url_or_hash);
                id = History.getIdByUrl(url) || false;
                if (id) {
                    State = History.getStateById(id);
                }
                if (!State && create && !History.isTraditionalAnchor(url_or_hash)) {
                    State = History.createStateObject(null, null, url);
                }
            }
            return State;
        };
        History.getIdByUrl = function (url) {
            var id = History.urlToId[url] || History.store.urlToId[url] || undefined;
            return id;
        };
        History.getLastSavedState = function () {
            return History.savedStates[History.savedStates.length - 1] || undefined;
        };
        History.getLastStoredState = function () {
            return History.storedStates[History.storedStates.length - 1] || undefined;
        };
        History.hasUrlDuplicate = function (newState) {
            var hasDuplicate = false,
                oldState;
            oldState = History.extractState(newState.url);
            hasDuplicate = oldState && oldState.id !== newState.id;
            return hasDuplicate;
        };
        History.storeState = function (newState) {
            History.urlToId[newState.url] = newState.id;
            History.storedStates.push(History.cloneObject(newState));
            return newState;
        };
        History.isLastSavedState = function (newState) {
            var isLast = false,
                newId, oldState, oldId;
            if (History.savedStates.length) {
                newId = newState.id;
                oldState = History.getLastSavedState();
                oldId = oldState.id;
                isLast = (newId === oldId);
            }
            return isLast;
        };
        History.saveState = function (newState) {
            if (History.isLastSavedState(newState)) {
                return false;
            }
            History.savedStates.push(History.cloneObject(newState));
            return true;
        };
        History.getStateByIndex = function (index) {
            var State = null;
            if (typeof index === 'undefined') {
                State = History.savedStates[History.savedStates.length - 1];
            } else if (index < 0) {
                State = History.savedStates[History.savedStates.length + index];
            } else {
                State = History.savedStates[index];
            }
            return State;
        };
        History.getCurrentIndex = function () {
            var index = null;
            if (History.savedStates.length < 1) {
                index = 0;
            } else {
                index = History.savedStates.length - 1;
            }
            return index;
        };
        History.getHash = function (doc) {
            var url = History.getLocationHref(doc),
                hash;
            hash = History.getHashByUrl(url);
            return hash;
        };
        History.unescapeHash = function (hash) {
            var result = History.normalizeHash(hash);
            result = decodeURIComponent(result);
            return result;
        };
        History.normalizeHash = function (hash) {
            var result = hash.replace(/[^#]*#/, '').replace(/#.*/, '');
            return result;
        };
        History.setHash = function (hash, queue) {
            var State, pageUrl;
            if (queue !== false && History.busy()) {
                History.pushQueue({
                    scope: History,
                    callback: History.setHash,
                    args: arguments,
                    queue: queue
                });
                return false;
            }
            History.busy(true);
            State = History.extractState(hash, true);
            if (State && !History.emulated.pushState) {
                History.pushState(State.data, State.title, State.url, false);
            } else if (History.getHash() !== hash) {
                if (History.bugs.setHash) {
                    pageUrl = History.getPageUrl();
                    History.pushState(null, null, pageUrl + '#' + hash, false);
                } else {
                    document.location.hash = hash;
                }
            }
            return History;
        };
        History.escapeHash = function (hash) {
            var result = History.normalizeHash(hash);
            result = window.encodeURIComponent(result);
            if (!History.bugs.hashEscape) {
                result = result.replace(/\%21/g, '!').replace(/\%26/g, '&').replace(/\%3D/g, '=').replace(/\%3F/g, '?');
            }
            return result;
        };
        History.getHashByUrl = function (url) {
            var hash = String(url).replace(/([^#]*)#?([^#]*)#?(.*)/, '$2');
            hash = History.unescapeHash(hash);
            return hash;
        };
        History.setTitle = function (newState) {
            var title = newState.title,
                firstState;
            if (!title) {
                firstState = History.getStateByIndex(0);
                if (firstState && firstState.url === newState.url) {
                    title = firstState.title || History.options.initialTitle;
                }
            }
            try {
                document.getElementsByTagName('title')[0].innerHTML = title.replace('<', '&lt;').replace('>', '&gt;').replace(' & ', ' &amp; ');
            } catch (Exception) {
            }
            document.title = title;
            return History;
        };
        History.queues = [];
        History.busy = function (value) {
            if (typeof value !== 'undefined') {
                History.busy.flag = value;
            } else if (typeof History.busy.flag === 'undefined') {
                History.busy.flag = false;
            }
            if (!History.busy.flag) {
                clearTimeout(History.busy.timeout);
                var fireNext = function () {
                    var i, queue, item;
                    if (History.busy.flag) return;
                    for (i = History.queues.length - 1; i >= 0; --i) {
                        queue = History.queues[i];
                        if (queue.length === 0) continue;
                        item = queue.shift();
                        History.fireQueueItem(item);
                        History.busy.timeout = setTimeout(fireNext, History.options.busyDelay);
                    }
                };
                History.busy.timeout = setTimeout(fireNext, History.options.busyDelay);
            }
            return History.busy.flag;
        };
        History.busy.flag = false;
        History.fireQueueItem = function (item) {
            return item.callback.apply(item.scope || History, item.args || []);
        };
        History.pushQueue = function (item) {
            History.queues[item.queue || 0] = History.queues[item.queue || 0] || [];
            History.queues[item.queue || 0].push(item);
            return History;
        };
        History.queue = function (item, queue) {
            if (typeof item === 'function') {
                item = {
                    callback: item
                };
            }
            if (typeof queue !== 'undefined') {
                item.queue = queue;
            }
            if (History.busy()) {
                History.pushQueue(item);
            } else {
                History.fireQueueItem(item);
            }
            return History;
        };
        History.clearQueue = function () {
            History.busy.flag = false;
            History.queues = [];
            return History;
        };
        History.stateChanged = false;
        History.doubleChecker = false;
        History.doubleCheckComplete = function () {
            History.stateChanged = true;
            History.doubleCheckClear();
            return History;
        };
        History.doubleCheckClear = function () {
            if (History.doubleChecker) {
                clearTimeout(History.doubleChecker);
                History.doubleChecker = false;
            }
            return History;
        };
        History.doubleCheck = function (tryAgain) {
            History.stateChanged = false;
            History.doubleCheckClear();
            if (History.bugs.ieDoubleCheck) {
                History.doubleChecker = setTimeout(function () {
                    History.doubleCheckClear();
                    if (!History.stateChanged) {
                        tryAgain();
                    }
                    return true;
                }, History.options.doubleCheckInterval);
            }
            return History;
        };
        History.safariStatePoll = function () {
            var
                urlState = History.extractState(History.getLocationHref()),
                newState;
            if (!History.isLastSavedState(urlState)) {
                newState = urlState;
            } else {
                return;
            }
            if (!newState) {
                newState = History.createStateObject();
            }
            History.Adapter.trigger(window, 'popstate');
            return History;
        };
        History.back = function (queue) {
            if (queue !== false && History.busy()) {
                History.pushQueue({
                    scope: History,
                    callback: History.back,
                    args: arguments,
                    queue: queue
                });
                return false;
            }
            History.busy(true);
            History.doubleCheck(function () {
                History.back(false);
            });
            history.go(-1);
            return true;
        };
        History.forward = function (queue) {
            if (queue !== false && History.busy()) {
                History.pushQueue({
                    scope: History,
                    callback: History.forward,
                    args: arguments,
                    queue: queue
                });
                return false;
            }
            History.busy(true);
            History.doubleCheck(function () {
                History.forward(false);
            });
            history.go(1);
            return true;
        };
        History.go = function (index, queue) {
            var i;
            if (index > 0) {
                for (i = 1; i <= index; ++i) {
                    History.forward(queue);
                }
            } else if (index < 0) {
                for (i = -1; i >= index; --i) {
                    History.back(queue);
                }
            } else {
                throw new Error('History.go: History.go requires a positive or negative integer passed.');
            }
            return History;
        };
        if (History.emulated.pushState) {
            var emptyFunction = function () {
            };
            History.pushState = History.pushState || emptyFunction;
            History.replaceState = History.replaceState || emptyFunction;
        } else {
            History.onPopState = function (event, extra) {
                var stateId = false,
                    newState = false,
                    currentHash, currentState;
                History.doubleCheckComplete();
                currentHash = History.getHash();
                if (currentHash) {
                    currentState = History.extractState(currentHash || History.getLocationHref(), true);
                    if (currentState) {
                        History.replaceState(currentState.data, currentState.title, currentState.url, false);
                    } else {
                        History.Adapter.trigger(window, 'anchorchange');
                        History.busy(false);
                    }
                    History.expectedStateId = false;
                    return false;
                }
                stateId = History.Adapter.extractEventData('state', event, extra) || false;
                if (stateId) {
                    newState = History.getStateById(stateId);
                } else if (History.expectedStateId) {
                    newState = History.getStateById(History.expectedStateId);
                } else {
                    newState = History.extractState(History.getLocationHref());
                }
                if (!newState) {
                    newState = History.createStateObject(null, null, History.getLocationHref());
                }
                History.expectedStateId = false;
                if (History.isLastSavedState(newState)) {
                    History.busy(false);
                    return false;
                }
                History.storeState(newState);
                History.saveState(newState);
                History.setTitle(newState);
                History.Adapter.trigger(window, 'statechange');
                History.busy(false);
                return true;
            };
            History.Adapter.bind(window, 'popstate', History.onPopState);
            History.pushState = function (data, title, url, queue) {
                if (History.getHashByUrl(url) && History.emulated.pushState) {
                    throw new Error('History.js does not support states with fragement-identifiers (hashes/anchors).');
                }
                if (queue !== false && History.busy()) {
                    History.pushQueue({
                        scope: History,
                        callback: History.pushState,
                        args: arguments,
                        queue: queue
                    });
                    return false;
                }
                History.busy(true);
                var newState = History.createStateObject(data, title, url);
                if (History.isLastSavedState(newState)) {
                    History.busy(false);
                } else {
                    History.storeState(newState);
                    History.expectedStateId = newState.id;
                    history.pushState(newState.id, newState.title, newState.url);
                    History.Adapter.trigger(window, 'popstate');
                }
                return true;
            };
            History.replaceState = function (data, title, url, queue) {
                if (History.getHashByUrl(url) && History.emulated.pushState) {
                    throw new Error('History.js does not support states with fragement-identifiers (hashes/anchors).');
                }
                if (queue !== false && History.busy()) {
                    History.pushQueue({
                        scope: History,
                        callback: History.replaceState,
                        args: arguments,
                        queue: queue
                    });
                    return false;
                }
                History.busy(true);
                var newState = History.createStateObject(data, title, url);
                if (History.isLastSavedState(newState)) {
                    History.busy(false);
                } else {
                    History.storeState(newState);
                    History.expectedStateId = newState.id;
                    history.replaceState(newState.id, newState.title, newState.url);
                    History.Adapter.trigger(window, 'popstate');
                }
                return true;
            };
        }
        if (sessionStorage) {
            try {
                History.store = JSON.parse(sessionStorage.getItem('History.store')) || {};
            } catch (err) {
                History.store = {};
            }
            History.normalizeStore();
        } else {
            History.store = {};
            History.normalizeStore();
        }
        History.Adapter.bind(window, "unload", History.clearAllIntervals);
        History.saveState(History.storeState(History.extractState(History.getLocationHref(), true)));
        if (sessionStorage) {
            History.onUnload = function () {
                var currentStore, item, currentStoreString;
                try {
                    currentStore = JSON.parse(sessionStorage.getItem('History.store')) || {};
                } catch (err) {
                    currentStore = {};
                }
                currentStore.idToState = currentStore.idToState || {};
                currentStore.urlToId = currentStore.urlToId || {};
                currentStore.stateToId = currentStore.stateToId || {};
                for (item in History.idToState) {
                    if (!History.idToState.hasOwnProperty(item)) {
                        continue;
                    }
                    currentStore.idToState[item] = History.idToState[item];
                }
                for (item in History.urlToId) {
                    if (!History.urlToId.hasOwnProperty(item)) {
                        continue;
                    }
                    currentStore.urlToId[item] = History.urlToId[item];
                }
                for (item in History.stateToId) {
                    if (!History.stateToId.hasOwnProperty(item)) {
                        continue;
                    }
                    currentStore.stateToId[item] = History.stateToId[item];
                }
                History.store = currentStore;
                History.normalizeStore();
                currentStoreString = JSON.stringify(currentStore);
                try {
                    sessionStorage.setItem('History.store', currentStoreString);
                } catch (e) {
                    if (e.code === DOMException.QUOTA_EXCEEDED_ERR) {
                        if (sessionStorage.length) {
                            sessionStorage.removeItem('History.store');
                            sessionStorage.setItem('History.store', currentStoreString);
                        } else {
                        }
                    } else {
                        throw e;
                    }
                }
            };
            History.intervalList.push(setInterval(History.onUnload, History.options.storeInterval));
            History.Adapter.bind(window, 'beforeunload', History.onUnload);
            History.Adapter.bind(window, 'unload', History.onUnload);
        }
        if (!History.emulated.pushState) {
            if (History.bugs.safariPoll) {
                History.intervalList.push(setInterval(History.safariStatePoll, History.options.safariPollInterval));
            }
            if (navigator.vendor === 'Apple Computer, Inc.' || (navigator.appCodeName || '') === 'Mozilla') {
                History.Adapter.bind(window, 'hashchange', function () {
                    History.Adapter.trigger(window, 'popstate');
                });
                if (History.getHash()) {
                    History.Adapter.onDomLoad(function () {
                        History.Adapter.trigger(window, 'hashchange');
                    });
                }
            }
        }
    };
    if (!History.options || !History.options.delayInit) {
        History.init();
    }
})(window);
(function (window, undefined) {
    "use strict";
    var
        History = window.History = window.History || {},
        jQuery = window.jQuery;
    if (typeof History.Adapter !== 'undefined') {
        throw new Error('History.js Adapter has already been loaded...');
    }
    History.Adapter = {
        bind: function (el, event, callback) {
            jQuery(el).bind(event, callback);
        },
        trigger: function (el, event, extra) {
            jQuery(el).trigger(event, extra);
        },
        extractEventData: function (key, event, extra) {
            var result = (event && event.originalEvent && event.originalEvent[key]) || (extra && extra[key]) || undefined;
            return result;
        },
        onDomLoad: function (callback) {
            jQuery(callback);
        }
    };
    if (typeof History.init !== 'undefined') {
        History.init();
    }
})(window);
;
var manualStateChange = true;
History.Adapter.bind(window, 'statechange', function (state) {
    if (!(manualStateChange === false)) {
        console.log("Back/Forward pressed");
        var state = History.getState();
        Buckty.handler(new Object(), state.url, true);
    }
    manualStateChange = true
});

(function ($) {

    var types = ['DOMMouseScroll', 'mousewheel'];

    if ($.event.fixHooks) {
        for (var i = types.length; i;) {
            $.event.fixHooks[types[--i]] = $.event.mouseHooks;
        }
    }

    $.event.special.mousewheel = {
        setup: function () {
            if (this.addEventListener) {
                for (var i = types.length; i;) {
                    this.addEventListener(types[--i], handler, false);
                }
            } else {
                this.onmousewheel = handler;
            }
        },

        teardown: function () {
            if (this.removeEventListener) {
                for (var i = types.length; i;) {
                    this.removeEventListener(types[--i], handler, false);
                }
            } else {
                this.onmousewheel = null;
            }
        }
    };

    $.fn.extend({
        mousewheel: function (fn) {
            return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
        },

        unmousewheel: function (fn) {
            return this.unbind("mousewheel", fn);
        }
    });


    function handler(event) {
        var orgEvent = event || window.event,
            args = [].slice.call(arguments, 1),
            delta = 0,
            returnValue = true,
            deltaX = 0,
            deltaY = 0;
        event = $.event.fix(orgEvent);
        event.type = "mousewheel";

        // Old school scrollwheel delta
        if (orgEvent.wheelDelta) {
            delta = orgEvent.wheelDelta / 120;
        }
        if (orgEvent.detail) {
            delta = -orgEvent.detail / 3;
        }

        // New school multidimensional scroll (touchpads) deltas
        deltaY = delta;

        // Gecko
        if (orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS) {
            deltaY = 0;
            deltaX = delta;
        }

        // Webkit
        if (orgEvent.wheelDeltaY !== undefined) {
            deltaY = orgEvent.wheelDeltaY / 120;
        }
        if (orgEvent.wheelDeltaX !== undefined) {
            deltaX = orgEvent.wheelDeltaX / 120;
        }

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

})(jQuery);

//Make sure jQuery has been loaded before app.js
if (typeof jQuery === "undefined") {
    throw new Error("AdminLTE requires jQuery");
}

/* AdminLTE
 *
 * @type Object
 * @description $.AdminLTE is the main object for the template's app.
 *              It's used for implementing functions and options related
 *              to the template. Keeping everything wrapped in an object
 *              prevents conflict with other plugins and is a better
 *              way to organize our code.
 */
$.AdminLTE = {};

/* --------------------
 * - AdminLTE Options -
 * --------------------
 * Modify these options to suit your implementation
 */
$.AdminLTE.options = {
    //Add slimscroll to navbar menus
    //This requires you to load the slimscroll plugin
    //in every page before app.js
    navbarMenuSlimscroll: true,
    navbarMenuSlimscrollWidth: "3px", //The width of the scroll bar
    navbarMenuHeight: "200px", //The height of the inner menu
    //General animation speed for JS animated elements such as box collapse/expand and
    //sidebar treeview slide up/down. This options accepts an integer as milliseconds,
    //'fast', 'normal', or 'slow'
    animationSpeed: 500,
    //Sidebar push menu toggle button selector
    sidebarToggleSelector: "[data-toggle='offcanvas']",
    //Activate sidebar push menu
    sidebarPushMenu: true,
    //Activate sidebar slimscroll if the fixed layout is set (requires SlimScroll Plugin)
    sidebarSlimScroll: true,
    //Enable sidebar expand on hover effect for sidebar mini
    //This option is forced to true if both the fixed layout and sidebar mini
    //are used together
    sidebarExpandOnHover: false,
    //BoxRefresh Plugin
    enableBoxRefresh: true,
    //Bootstrap.js tooltip
    enableBSToppltip: true,
    BSTooltipSelector: "[data-toggle='tooltip']",
    //Enable Fast Click. Fastclick.js creates a more
    //native touch experience with touch devices. If you
    //choose to enable the plugin, make sure you load the script
    //before AdminLTE's app.js
    enableFastclick: true,
    //Control Sidebar Options
    enableControlSidebar: true,
    controlSidebarOptions: {
        //Which button should trigger the open/close event
        toggleBtnSelector: "[data-toggle='control-sidebar']",
        //The sidebar selector
        selector: ".control-sidebar",
        //Enable slide over content
        slide: true
    },
    //Box Widget Plugin. Enable this plugin
    //to allow boxes to be collapsed and/or removed
    enableBoxWidget: true,
    //Box Widget plugin options
    boxWidgetOptions: {
        boxWidgetIcons: {
            //Collapse icon
            collapse: 'fa-minus',
            //Open icon
            open: 'fa-plus',
            //Remove icon
            remove: 'fa-times'
        },
        boxWidgetSelectors: {
            //Remove button selector
            remove: '[data-widget="remove"]',
            //Collapse button selector
            collapse: '[data-widget="collapse"]'
        }
    },
    //Direct Chat plugin options
    directChat: {
        //Enable direct chat by default
        enable: true,
        //The button to open and close the chat contacts pane
        contactToggleSelector: '[data-widget="chat-pane-toggle"]'
    },
    //Define the set of colors to use globally around the website
    colors: {
        lightBlue: "#3c8dbc",
        red: "#f56954",
        green: "#00a65a",
        aqua: "#00c0ef",
        yellow: "#f39c12",
        blue: "#0073b7",
        navy: "#001F3F",
        teal: "#39CCCC",
        olive: "#3D9970",
        lime: "#01FF70",
        orange: "#FF851B",
        fuchsia: "#F012BE",
        purple: "#8E24AA",
        maroon: "#D81B60",
        black: "#222222",
        gray: "#d2d6de"
    },
    //The standard screen sizes that bootstrap uses.
    //If you change these in the variables.less file, change
    //them here too.
    screenSizes: {
        xs: 480,
        sm: 768,
        md: 992,
        lg: 1200
    }
};

/* ------------------
 * - Implementation -
 * ------------------
 * The next block of code implements AdminLTE's
 * functions and plugins as specified by the
 * options above.
 */
$(function () {
    "use strict";

    //Fix for IE page transitions
    $("body").removeClass("hold-transition");

    //Extend options if external options exist
    if (typeof AdminLTEOptions !== "undefined") {
        $.extend(true,
            $.AdminLTE.options,
            AdminLTEOptions);
    }

    //Easy access to options
    var o = $.AdminLTE.options;

    //Set up the object
    _init();

    //Activate the layout maker
    $.AdminLTE.layout.activate();

    //Enable sidebar tree view controls
    $.AdminLTE.tree('.sidebar');

    //Enable control sidebar
    if (o.enableControlSidebar) {
        $.AdminLTE.controlSidebar.activate();
    }

    //Add slimscroll to navbar dropdown
    if (o.navbarMenuSlimscroll && typeof $.fn.slimscroll != 'undefined') {
        $(".navbar .menu").slimscroll({
            height: o.navbarMenuHeight,
            alwaysVisible: false,
            size: o.navbarMenuSlimscrollWidth
        }).css("width", "100%");
    }

    //Activate sidebar push menu
    if (o.sidebarPushMenu) {
        $.AdminLTE.pushMenu.activate(o.sidebarToggleSelector);
    }

    //Activate Bootstrap tooltip
    if (o.enableBSToppltip) {
        $('body').tooltip({
            selector: o.BSTooltipSelector
        });
    }

    //Activate box widget
    if (o.enableBoxWidget) {
        $.AdminLTE.boxWidget.activate();
    }

    //Activate fast click
    if (o.enableFastclick && typeof FastClick != 'undefined') {
        FastClick.attach(document.body);
    }

    //Activate direct chat widget
    if (o.directChat.enable) {
        $(document).on('click', o.directChat.contactToggleSelector, function () {
            var box = $(this).parents('.direct-chat').first();
            box.toggleClass('direct-chat-contacts-open');
        });
    }

    /*
     * INITIALIZE BUTTON TOGGLE
     * ------------------------
     */
    $('.btn-group[data-toggle="btn-toggle"]').each(function () {
        var group = $(this);
        $(this).find(".btn").on('click', function (e) {
            group.find(".btn.active").removeClass("active");
            $(this).addClass("active");
            e.preventDefault();
        });

    });
});

/* ----------------------------------
 * - Initialize the AdminLTE Object -
 * ----------------------------------
 * All AdminLTE functions are implemented below.
 */
function _init() {
    'use strict';
    /* Layout
     * ======
     * Fixes the layout height in case min-height fails.
     *
     * @type Object
     * @usage $.AdminLTE.layout.activate()
     *        $.AdminLTE.layout.fix()
     *        $.AdminLTE.layout.fixSidebar()
     */
    $.AdminLTE.layout = {
        activate: function () {
            var _this = this;
            _this.fix();
            _this.fixSidebar();
            $(window, ".wrapper").resize(function () {
                _this.fix();
                _this.fixSidebar();
            });
        },
        fix: function () {
            //Get window height and the wrapper height
            var neg = $('.main-header').outerHeight() + $('.main-footer').outerHeight();
            var window_height = $(window).height();
            var sidebar_height = $(".sidebar").height();
            //Set the min-height of the content and sidebar based on the
            //the height of the document.
            if ($("body").hasClass("fixed")) {
                $(".content-wrapper, .right-side").css('min-height', window_height - $('.main-footer').outerHeight());
            } else {
                var postSetWidth;
                if (window_height >= sidebar_height) {
                    $(".content-wrapper, .right-side").css('min-height', window_height - neg);
                    postSetWidth = window_height - neg;
                } else {
                    $(".content-wrapper, .right-side").css('min-height', sidebar_height);
                    postSetWidth = sidebar_height;
                }

                //Fix for the control sidebar height
                var controlSidebar = $($.AdminLTE.options.controlSidebarOptions.selector);
                if (typeof controlSidebar !== "undefined") {
                    if (controlSidebar.height() > postSetWidth)
                        $(".content-wrapper, .right-side").css('min-height', controlSidebar.height());
                }

            }
        },
        fixSidebar: function () {
            //Make sure the body tag has the .fixed class
            if (!$("body").hasClass("fixed")) {
                if (typeof $.fn.slimScroll != 'undefined') {
                    $(".sidebar").slimScroll({
                        destroy: true
                    }).height("auto");
                }
                return;
            } else if (typeof $.fn.slimScroll == 'undefined' && window.console) {
                window.console.error("Error: the fixed layout requires the slimscroll plugin!");
            }
            //Enable slimscroll for fixed layout
            if ($.AdminLTE.options.sidebarSlimScroll) {
                if (typeof $.fn.slimScroll != 'undefined') {
                    //Destroy if it exists
                    $(".sidebar").slimScroll({
                        destroy: true
                    }).height("auto");
                    //Add slimscroll
                    $(".sidebar").slimscroll({
                        height: ($(window).height() - $(".main-header").height()) + "px",
                        color: "rgba(0,0,0,0.2)",
                        size: "3px"
                    });
                }
            }
        }
    };

    /* PushMenu()
     * ==========
     * Adds the push menu functionality to the sidebar.
     *
     * @type Function
     * @usage: $.AdminLTE.pushMenu("[data-toggle='offcanvas']")
     */
    $.AdminLTE.pushMenu = {
        activate: function (toggleBtn) {
            //Get the screen sizes
            var screenSizes = $.AdminLTE.options.screenSizes;

            //Enable sidebar toggle
            $(toggleBtn).on('click', function (e) {
                e.preventDefault();

                //Enable sidebar push menu
                if ($(window).width() > (screenSizes.sm - 1)) {
                    if ($("body").hasClass('sidebar-collapse')) {
                        $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
                    } else {
                        $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
                    }
                }
                //Handle sidebar push menu for small screens
                else {
                    if ($("body").hasClass('sidebar-open')) {
                        $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
                    } else {
                        $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
                    }
                }
            });

            $(".content-wrapper").click(function () {
                //Enable hide menu when clicking on the content-wrapper on small screens
                if ($(window).width() <= (screenSizes.sm - 1) && $("body").hasClass("sidebar-open")) {
                    $("body").removeClass('sidebar-open');
                }
            });

            //Enable expand on hover for sidebar mini
            if ($.AdminLTE.options.sidebarExpandOnHover || ($('body').hasClass('fixed') && $('body').hasClass('sidebar-mini'))) {
                this.expandOnHover();
            }
        },
        expandOnHover: function () {
            var _this = this;
            var screenWidth = $.AdminLTE.options.screenSizes.sm - 1;
            //Expand sidebar on hover
            $('.main-sidebar').hover(function () {
                if ($('body').hasClass('sidebar-mini') && $("body").hasClass('sidebar-collapse') && $(window).width() > screenWidth) {
                    _this.expand();
                }
            }, function () {
                if ($('body').hasClass('sidebar-mini') && $('body').hasClass('sidebar-expanded-on-hover') && $(window).width() > screenWidth) {
                    _this.collapse();
                }
            });
        },
        expand: function () {
            $("body").removeClass('sidebar-collapse').addClass('sidebar-expanded-on-hover');
        },
        collapse: function () {
            if ($('body').hasClass('sidebar-expanded-on-hover')) {
                $('body').removeClass('sidebar-expanded-on-hover').addClass('sidebar-collapse');
            }
        }
    };

    /* Tree()
     * ======
     * Converts the sidebar into a multilevel
     * tree view menu.
     *
     * @type Function
     * @Usage: $.AdminLTE.tree('.sidebar')
     */
    $.AdminLTE.tree = function (menu) {
        var _this = this;
        var animationSpeed = $.AdminLTE.options.animationSpeed;
        $(document).on('click', menu + ' li a', function (e) {
            //Get the clicked link and the next element
            var $this = $(this);
            var checkElement = $this.next();

            //Check if the next element is a menu and is visible
            if ((checkElement.is('.treeview-menu')) && (checkElement.is(':visible'))) {
                //Close the menu
                checkElement.slideUp(animationSpeed, function () {
                    checkElement.removeClass('menu-open');
                    //Fix the layout in case the sidebar stretches over the height of the window
                    //_this.layout.fix();
                });
                checkElement.parent("li").removeClass("active");
            }
            //If the menu is not visible
            else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
                //Get the parent menu
                var parent = $this.parents('ul').first();
                //Close all open menus within the parent
                var ul = parent.find('ul:visible').slideUp(animationSpeed);
                //Remove the menu-open class from the parent
                ul.removeClass('menu-open');
                //Get the parent li
                var parent_li = $this.parent("li");

                //Open the target menu and add the menu-open class
                checkElement.slideDown(animationSpeed, function () {
                    //Add the class active to the parent li
                    checkElement.addClass('menu-open');
                    parent.find('li.active').removeClass('active');
                    parent_li.addClass('active');
                    //Fix the layout in case the sidebar stretches over the height of the window
                    _this.layout.fix();
                });
            }
            //if this isn't a link, prevent the page from being redirected
            if (checkElement.is('.treeview-menu')) {
                e.preventDefault();
            }
        });
    };

    /* ControlSidebar
     * ==============
     * Adds functionality to the right sidebar
     *
     * @type Object
     * @usage $.AdminLTE.controlSidebar.activate(options)
     */
    $.AdminLTE.controlSidebar = {
        //instantiate the object
        activate: function () {
            //Get the object
            var _this = this;
            //Update options
            var o = $.AdminLTE.options.controlSidebarOptions;
            //Get the sidebar
            var sidebar = $(o.selector);
            //The toggle button
            var btn = $(o.toggleBtnSelector);

            //Listen to the click event
            btn.on('click', function (e) {
                e.preventDefault();
                //If the sidebar is not open
                if (!sidebar.hasClass('control-sidebar-open') && !$('body').hasClass('control-sidebar-open')) {
                    //Open the sidebar
                    _this.open(sidebar, o.slide);
                } else {
                    _this.close(sidebar, o.slide);
                }
            });

            //If the body has a boxed layout, fix the sidebar bg position
            var bg = $(".control-sidebar-bg");
            _this._fix(bg);

            //If the body has a fixed layout, make the control sidebar fixed
            if ($('body').hasClass('fixed')) {
                _this._fixForFixed(sidebar);
            } else {
                //If the content height is less than the sidebar's height, force max height
                if ($('.content-wrapper, .right-side').height() < sidebar.height()) {
                    _this._fixForContent(sidebar);
                }
            }
        },
        //Open the control sidebar
        open: function (sidebar, slide) {
            //Slide over content
            if (slide) {
                sidebar.addClass('control-sidebar-open');
            } else {
                //Push the content by adding the open class to the body instead
                //of the sidebar itself
                $('body').addClass('control-sidebar-open');
            }
        },
        //Close the control sidebar
        close: function (sidebar, slide) {
            if (slide) {
                sidebar.removeClass('control-sidebar-open');
            } else {
                $('body').removeClass('control-sidebar-open');
            }
        },
        _fix: function (sidebar) {
            var _this = this;
            if ($("body").hasClass('layout-boxed')) {
                sidebar.css('position', 'absolute');
                sidebar.height($(".wrapper").height());
                $(window).resize(function () {
                    _this._fix(sidebar);
                });
            } else {
                sidebar.css({
                    'position': 'fixed',
                    'height': 'auto'
                });
            }
        },
        _fixForFixed: function (sidebar) {
            sidebar.css({
                'position': 'fixed',
                'max-height': '100%',
                'overflow': 'auto',
                'padding-bottom': '50px'
            });
        },
        _fixForContent: function (sidebar) {
            $(".content-wrapper, .right-side").css('min-height', sidebar.height());
        }
    };

    /* BoxWidget
     * =========
     * BoxWidget is a plugin to handle collapsing and
     * removing boxes from the screen.
     *
     * @type Object
     * @usage $.AdminLTE.boxWidget.activate()
     *        Set all your options in the main $.AdminLTE.options object
     */
    $.AdminLTE.boxWidget = {
        selectors: $.AdminLTE.options.boxWidgetOptions.boxWidgetSelectors,
        icons: $.AdminLTE.options.boxWidgetOptions.boxWidgetIcons,
        animationSpeed: $.AdminLTE.options.animationSpeed,
        activate: function (_box) {
            var _this = this;
            if (!_box) {
                _box = document; // activate all boxes per default
            }
            //Listen for collapse event triggers
            $(_box).on('click', _this.selectors.collapse, function (e) {
                e.preventDefault();
                _this.collapse($(this));
            });

            //Listen for remove event triggers
            $(_box).on('click', _this.selectors.remove, function (e) {
                e.preventDefault();
                _this.remove($(this));
            });
        },
        collapse: function (element) {
            var _this = this;
            //Find the box parent
            var box = element.parents(".box").first();
            //Find the body and the footer
            var box_content = box.find("> .box-body, > .box-footer, > form  >.box-body, > form > .box-footer");
            if (!box.hasClass("collapsed-box")) {
                //Convert minus into plus
                element.children(":first")
                    .removeClass(_this.icons.collapse)
                    .addClass(_this.icons.open);
                //Hide the content
                box_content.slideUp(_this.animationSpeed, function () {
                    box.addClass("collapsed-box");
                });
            } else {
                //Convert plus into minus
                element.children(":first")
                    .removeClass(_this.icons.open)
                    .addClass(_this.icons.collapse);
                //Show the content
                box_content.slideDown(_this.animationSpeed, function () {
                    box.removeClass("collapsed-box");
                });
            }
        },
        remove: function (element) {
            //Find the box parent
            var box = element.parents(".box").first();
            box.slideUp(this.animationSpeed);
        }
    };
}

/* ------------------
 * - Custom Plugins -
 * ------------------
 * All custom plugins are defined below.
 */

/*
 * BOX REFRESH BUTTON
 * ------------------
 * This is a custom plugin to use with the component BOX. It allows you to add
 * a refresh button to the box. It converts the box's state to a loading state.
 *
 * @type plugin
 * @usage $("#box-widget").boxRefresh( options );
 */
(function ($) {

    "use strict";

    $.fn.boxRefresh = function (options) {

        // Render options
        var settings = $.extend({
            //Refresh button selector
            trigger: ".refresh-btn",
            //File source to be loaded (e.g: ajax/src.php)
            source: "",
            //Callbacks
            onLoadStart: function (box) {
                return box;
            }, //Right after the button has been clicked
            onLoadDone: function (box) {
                return box;
            } //When the source has been loaded

        }, options);

        //The overlay
        var overlay = $('<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>');

        return this.each(function () {
            //if a source is specified
            if (settings.source === "") {
                if (window.console) {
                    window.console.log("Please specify a source first - boxRefresh()");
                }
                return;
            }
            //the box
            var box = $(this);
            //the button
            var rBtn = box.find(settings.trigger).first();

            //On trigger click
            rBtn.on('click', function (e) {
                e.preventDefault();
                //Add loading overlay
                start(box);

                //Perform ajax call
                box.find(".box-body").load(settings.source, function () {
                    done(box);
                });
            });
        });

        function start(box) {
            //Add overlay and loading img
            box.append(overlay);

            settings.onLoadStart.call(box);
        }

        function done(box) {
            //Remove overlay and loading img
            box.find(overlay).remove();

            settings.onLoadDone.call(box);
        }

    };

})(jQuery);

/*
 * EXPLICIT BOX ACTIVATION
 * -----------------------
 * This is a custom plugin to use with the component BOX. It allows you to activate
 * a box inserted in the DOM after the app.js was loaded.
 *
 * @type plugin
 * @usage $("#box-widget").activateBox();
 */
(function ($) {

    'use strict';

    $.fn.activateBox = function () {
        $.AdminLTE.boxWidget.activate(this);
    };

})(jQuery);

/*
 * TODO LIST CUSTOM PLUGIN
 * -----------------------
 * This plugin depends on iCheck plugin for checkbox and radio inputs
 *
 * @type plugin
 * @usage $("#todo-widget").todolist( options );
 */
(function ($) {

    'use strict';

    $.fn.todolist = function (options) {
        // Render options
        var settings = $.extend({
            //When the user checks the input
            onCheck: function (ele) {
                return ele;
            },
            //When the user unchecks the input
            onUncheck: function (ele) {
                return ele;
            }
        }, options);

        return this.each(function () {

            if (typeof $.fn.iCheck != 'undefined') {
                $('input', this).on('ifChecked', function () {
                    var ele = $(this).parents("li").first();
                    ele.toggleClass("done");
                    settings.onCheck.call(ele);
                });

                $('input', this).on('ifUnchecked', function () {
                    var ele = $(this).parents("li").first();
                    ele.toggleClass("done");
                    settings.onUncheck.call(ele);
                });
            } else {
                $('input', this).on('change', function () {
                    var ele = $(this).parents("li").first();
                    ele.toggleClass("done");
                    if ($('input', ele).is(":checked")) {
                        settings.onCheck.call(ele);
                    } else {
                        settings.onUncheck.call(ele);
                    }
                });
            }
        });
    };
}(jQuery));
var Buckty = {
    clean: function (i) {
        document.getElementById(i).innerHTML = '';
    },
    LoadFuncs: function () {

        jQuery('#settings').submit(function (e) {
            var data = $(this).serializeArray();
            data.push({
                name: 'csrf_Buckty',
                value: Buckty.Buckty_getToken
            });
            $.ajax({
                url: site_url + 'admin/ajaxcall?action=saveSettings',
                type: 'post',
                data: data,
                dataType: 'json',
                beforeSend: function () {
                    jQuery('#settings').append('<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>');
                },
                success: function (data) {
                    if (data.error_code === 1) {
                        Buckty.toast('info', data.message);
                    } else if (data.error_code === 0) {
                        Buckty.toast('check-circle-o', data.message);
                    }
                    jQuery('#settings .overlay').remove();
                },
                error: function (error) {
                    var error_class = 'alert-error';
                    var container = jQuery('#settings_');
                    container.addClass(error_class).html(error).show();
                }
            });
            e.preventDefault();
        });
        jQuery('#upload_limit').keyup(function () {
            var num = $(this).val();
            var converted = Buckty.bytes(num);
            jQuery('#updated_qoute').text(converted);
        });


        jQuery('#make_language').submit(function (e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            data.push({
                name: 'csrf_Buckty',
                value: Buckty.Buckty_getToken
            });
            $.ajax({
                url: site_url + 'admin/ajaxcall?action=saveLanguage',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.error_code === 1) {
                        Buckty.toast('info', data.message);
                    } else if (data.error_code === 0) {
                        Buckty.handler('', location.href);
                    }
                }
            });
        });

        jQuery('#add_key').submit(function (e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            data.push({
                name: 'csrf_Buckty',
                value: Buckty.Buckty_getToken
            });
            $.ajax({
                url: site_url + 'admin/ajaxcall?action=addlangKeys',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (data) {
                    var container = jQuery('#keysappend');
                    if (data.error_code === 1) {

                    } else if (data.error_code === 0) {
                        $.each(data.data, function (data, event) {
                            container.append(event);
                        });
                        jQuery('#add_key').reset();
                    }
                }
            });
        });

        jQuery('#Socialsettings').submit(function (e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            data.push({
                name: 'csrf_Buckty',
                value: Buckty.Buckty_getToken
            });
            $.ajax({
                url: site_url + 'admin/ajaxcall?action=saveApiSettings',
                type: 'post',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.error_code === 1) {
                        Buckty.toast('info', data.message);
                    } else if (data.error_code === 0) {
                        Buckty.toast('check-circle-o', data.message);
                    }
                    jQuery('#settings .overlay').remove();
                },
                error: function (error) {
                    var error_class = 'alert-error';
                    var container = jQuery('#settings_');
                    container.addClass(error_class).html(error).show();
                }
            });
        });
    },
    bytes: function (bytes) {
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    },
    deleteLang: function (li) {
        $.ajax({
            url: site_url + 'admin/ajaxcall?action=delLang',
            type: 'get',
            data: {
                csrf_Buckty: Buckty.Buckty_getToken,
                langId: li
            },
            processData: true,
            dataType: 'json',
            success: function (d) {
                if (d.error_code === 0) {
                    jQuery('#lang_' + li).slideUp().delay(2000).remove();
                }
            }
        });
    },
    getLangFields: function (i) {
        $.ajax({
            url: site_url + 'admin/ajaxcall?action=getFieldsLang',
            type: 'get',
            data: {
                csrf_Buckty: Buckty.Buckty_getToken,
                id: i
            },
            dataType: 'json',
            beforeSend: function () {
                jQuery('#lang_fields').show(), jQuery('#lang_fields').append('<div class="overlay"><div class="fa fa-refresh fa-spin"></div></div>');
            },
            success: function (l) {
                jQuery('.overlay').remove();
                var c = jQuery('#fields_append');
                Buckty.clean('fields_append');
                if (l.empty === 'yes') {
                    c.append('<div id="success_o_error" style="display:none;" class="alert alert-error alert-dismissible">' +
                        'Found 0 Fields' +
                        '</div>');
                    jQuery('#success_o_error').show();
                } else {

                    $.each(l.data, function (data, event) {
                        var dom = '<tr>' +
                            '<td><input type="text" class="form-control" readonly name="fields_lang['+event.keyname+'_'+event.lang_id+']" value="' + event.key_name + '" /></td>' +
                            '<td><input type="text" class="form-control" name="fields_value['+event.keyname+'_'+event.lang_id+']" value="' + event.value + '" /></td>';
                        c.append(dom);
                    });

                    $('#lang_update').show();
                }
            }
        })
    },
    setLang: function (i) {
        $.ajax({
            url: site_url + 'admin/ajaxcall',
            type: 'get',
            data: {
                action: 'setOfficialLang',
                csrf_Buckty: Buckty.Buckty_getToken,
                L: i
            },
            dataType: 'json',
            success: function (d) {
                if (d.error_code === 0) {
                    location.reload();
                } else {
                    console.log(d.message);
                }
            }
        });
    },
    saveTranslation: function (i) {
        var data = i.serializeArray();
        data.push({name:'csrf_Buckty',value:Buckty.Buckty_getToken});
        data.push({name:'action',value:'saveTranslation'});
        $.ajax({
            url: site_url + 'admin/ajaxcall',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (d) {
                if (d.error_code === 0) {
                    Buckty.toast('check-circle-o', d.message);
                } else {
                    Buckty.toast('info', d.message);
                }
            }
        });
        return false;
    },
    user: {
        edit: function (e) {
            var user_id = e.attr('data-id');
            $.get(site_url + 'admin/edit_user', {
                csrf_Buckty: Buckty.Buckty_getToken,
                user_id: user_id
            }).done(function (r) {
                var container = $('#editUser .modal-body');
                container.html(r);
            })
        },
        update: function (e) {
            var data = e.serializeArray();
            data.push({
                name: 'csrf_Buckty',
                value: Buckty.Buckty_getToken
            });
            $.post(site_url + 'admin/update_user', data).done(function (r) {
                var r = JSON.parse(r);
                if (r.error_code === 0) {
                    location.reload();
                } else if (r.error_code === 1) {
                    Buckty.toast('info', r.message);
                }
            });
            return false;
        },
        remove: function (e) {
            var user_id = e.attr('data-id');
            var usrs = [];
            usrs.push(user_id);
            $.post(site_url + 'admin/removeuser', {
                csrf_Buckty: Buckty.Buckty_getToken,
                users: usrs
            }).done(function (r) {
                var r = JSON.parse(r);
                if(r.error_code === 0){
                    $('#user_'+user_id).remove();
                    Buckty.toast('check-circle-o', r.message);
                } else if(r.error_code === 1){
                    Buckty.toast('info', r.message);
                }
            });
        },
        adduser: function (e) {
            var data = e.serializeArray();
            data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});

            $.post(site_url + 'admin/adduser', data).done(function (r) {
                var r = JSON.parse(r);
                var error_container = $('#addUserErrors');
                if (r.code === 1) {
                    error_container.text(r.message);
                } else {
                    Buckty.handler('', location.href);
                    $('#addUser').modal('toggle');
                }
            });
            return false;
        },
        search: function (e) {
            Buckty.loading('s');
            var data = e.serializeArray();
            var text = e.find('input[name="user_search"]').val();
            data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
            if (text !== '') {
                $.get(site_url + 'admin/searchusers', data).done(function (r) {
                    var container = $('#appendusers');
                    container.html(r);
                    Buckty.loading('h');
                });
            } else {
                Buckty.handler('', location.href);
            }

            return false;
        },
        ban: function (e) {
            var id = e.attr('data-id');
            var what = e.attr('data-what');
            $.post(site_url + 'admin/userban', {
                csrf_Buckty: Buckty.Buckty_getToken,
                hash: id,
                what: what
            }).done(function (r) {
                r = JSON.parse(r);
                if (r.error_code === 0) {
                    Buckty.toast('check-circle-o', r.message);
                    if (what === 'u')
                        e.attr('data-what', 'b'), e.text('Banned');
                    else
                        e.attr('data-what', 'u'), e.text('Un-banned');

                    Buckty.handler('', location.href);
                } else if (r.error_code === 1) {
                    Buckty.toast('info', r.message);
                }
            });
        }
    },
    folder: {
        delete: function (e) {
            var h = [];
            h.push(e.attr('data-id') + '/' + 'folder');
            $.post(site_url + 'useraction/removeitem', {
                csrf_Buckty: Buckty.Buckty_getToken,
                hash: h,
                who: 2
            }).done(function (r) {
                var r = JSON.parse(r);
                if (r.error_code === 0) {
                    Buckty.toast('trash', r.msg.message);
                    Buckty.handler('', location.href);
                } else if (r.error_code === 1) {
                    Buckty.toast('info', r.msg.message);
                }
            });
        },
        search: function (e) {
            Buckty.loading('s');
            var data = e.serializeArray();
            var text = e.find('input[name="folder_search"]').val();
            data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
            data.push({name: 'type', value: 'folder'});
            if (text !== '') {
                $.get(site_url + 'admin/searchitem', data).done(function (r) {
                    var container = $('#appendfolders');
                    container.html(r);
                    Buckty.loading('h');
                });
            } else {
                Buckty.handler('', location.href);
            }

            return false;
        }
    },
    file: {
        delete: function (e) {
            var h = [];
            h.push(e.attr('data-id') + '/' + 'file');
            $.post(site_url + 'useraction/removeitem', {
                csrf_Buckty: Buckty.Buckty_getToken,
                hash: h,
                who: 2
            }).done(function (r) {
                var r = JSON.parse(r);
                if (r.error_code === 0) {
                    Buckty.toast('trash', r.msg.message);
                    Buckty.handler('', location.href);
                } else if (r.error_code === 1) {
                    Buckty.toast('info', r.msg.message);
                }
            });
        },
        search: function (e) {
            Buckty.loading('s');
            var data = e.serializeArray();
            var text = e.find('input[name="folder_search"]').val();
            data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
            data.push({name: 'type', value: 'file'});
            if (text !== '') {
                $.get(site_url + 'admin/searchitem', data).done(function (r) {
                    var container = $('#appendfiles');
                    container.html(r);
                    Buckty.loading('h');
                });
            } else {
                Buckty.handler('', location.href);
            }

            return false;
        }
    },
    Buckty_getToken: function () {
        return csrf_token;
    },
    handler: function (event, url, manual) {
        if (typeof manual === "undefined") {
            manual = false;
        }
        if (typeof url === "undefined") {
            link = $(this);
            if (link.data('no-ajax') === true)
                return;
            var href = link.attr("href"),
                target = (typeof link.data('target') !== "undefined") ? link.data('target') : '.content-wrapper',
                append = (typeof link.data('append') !== "undefined") ? link.data('append') : false,
                changeUrl = (typeof link.data('change-url') === "undefined") ? true : link.data('change-url'),
                type = (typeof link.data('type') !== "undefined") ? link.data('type') : 'GET';
            if (!href || href === "#" || href === "javascript:void(0);" || href === "javascript:void(0)")
                return;
        } else {
            target = '.content-wrapper';
            type = "GET";
            append = false;
            changeUrl = true;
            var href = url;
        }
        $.ajax({
            type: type,
            url: href,
            async: true,
            beforeSend: function () {
                Buckty.loading('s');
            }
        }).always(function () {
            Buckty.loading('h');
        }).done(function (data) {
            var content = $(data).find('.content-wrapper').html();
            var main_script = $(data).filter('#js_main_objects').html();
            //var folder_ = $(data).filter('meta[name=current_folder]').attr('content');
            var matches = data.match(/<title>(.*?)<\/title>/);
            if (matches) {
                var title = matches[1];
            }
            if (title)
                document.title = title;
            if (content) {
                if (append === false)
                    $(target).html(content);
                else
                    $(target).append(content);
            } else
                $(target).html(data);
            if (changeUrl) {
                manualStateChange = manual;
                History.pushState({}, document.title, href);
            }
            if (main_script) {
                $('#js_main_objects').remove();
                $("<script id='#js_main_objects'/>").text(main_script).appendTo("head");
                //$('meta[name=current_folder]').attr('content', folder_);
            }
            Buckty.LoadFuncs();
            Buckty.printChart();
        });
        return false;
    },
    loading: function (t) {
        switch (t) {
            case 's':
                jQuery('.toast_container > .loading').show().animate({
                    bottom: '0px'
                }, 100);
                break;
            case 'h':
                jQuery('.toast_container > .loading').animate({
                    bottom: '-300px'
                }, 100).hide();
                ;
                break;
        }
    },
    toast: function (i, c) {
        var num = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
        ;
        var container = jQuery('#toast-container');
        container.append('<div id="notification-' + num + '"><i class="fa fa-' + i + '"></i><span class="text">' + c + '</span></div>');
        setTimeout(function () {
            jQuery('#notification-' + num).animate({
                bottom: '-1000px'
            }, 3000, function () {
                jQuery('#notification-' + num).remove();
            });
        }, 5000);
    },
    page: {
        save: function (e) {
            var data = e.serializeArray();
            data.push({name: 'pageBody', value: CKEDITOR.instances.editor1.getData()});
            data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
            $.post(site_url + 'admin/savepage', data).done(function (r) {
                r = JSON.parse(r);
                if (r.error_code === 0) {
                    Buckty.toast('check-cricle-o', r.message);
                } else if (r.error_code === 1) {
                    Buckty.toast('info', r.message);
                }
            });
            return false;
        },
        publish: function (e) {
            var data = e.serializeArray();
            data.push({name: 'pageBody', value: CKEDITOR.instances.editor1.getData()});
            data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
            $.post(site_url + 'admin/publishpage', data).done(function (r) {
                r = JSON.parse(r);
                if (r.error_code === 0) {
                    Buckty.toast('check-cricle-o', r.message);
                } else if (r.error_code === 1) {
                    Buckty.toast('info', r.message);
                }
            });
            return false;
        },
        removePage: function(id){
            $.post(site_url + 'admin/removepage',{csrf_Buckty:Buckty.Buckty_getToken,id:id}).done(function(r){
                r = JSON.parse(r);
                if(r.error_code === 0){
                    Buckty.handler('',location.href);
                    Buckty.toast('check-circle-o', r.message);
                } else {
                    Buckty.toast('info', r.message);
                }
            });
        }
    },
    key: {
     remove: function(key){
         $.post(site_url + 'admin/removekey',{csrf_Buckty:Buckty.Buckty_getToken,key:key}).done(function(r){
             var container = $('#keysappend');
             var content = $(r).find('#keysappend').html();
             container.html(content);
         });
     }
    },
    printChart: function () {
        var data;
        $.getJSON(site_url + 'admin/loadstats').done(function (r) {
            var file_Data = {
                labels: r.files.dates,
                datasets: [
                    {
                        label: "My First dataset",
                        fillColor: "rgba(220,220,220,0.2)",
                        strokeColor: "rgba(220,220,220,1)",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: r.files.counts
                    }
                ]


            }
            var folder_Data = {
                labels: r.folders.dates,
                datasets: [
                    {
                        label: "My First dataset",
                        fillColor: "rgba(220,220,220,0.2)",
                        strokeColor: "rgba(220,220,220,1)",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: r.folders.counts
                    }
                ]


            }

            if($('#filesChart').length) {
                var files = document.getElementById("filesChart").getContext("2d");
                window.myLine = new Chart(files).Line(file_Data, {
                    responsive: true
                });
                var folders = document.getElementById("foldersChart").getContext("2d");
                window.myLine = new Chart(folders).Line(folder_Data, {
                    responsive: true
                });
            }
        });
    },
    saveSmtp: function(e){
        var data = e.serializeArray();
        data.push({name:'csrf_Buckty',value:Buckty.Buckty_getToken});
        $.post(site_url + 'admin/updatesmtp',data).done(function(r){
            r = JSON.parse(r);
            if (r.error_code === 1) {
                Buckty.toast('info', r.message);
            } else if (r.error_code === 0) {
                Buckty.toast('check-circle-o', r.message);
            }
        });
        return false;
    }

}
$(function () {
    Buckty.LoadFuncs();
    $(document.body).on('click', 'a', Buckty.handler);
});

window.onload = function () {
    Buckty.printChart();
}
