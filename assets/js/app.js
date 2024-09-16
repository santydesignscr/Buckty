/*!
 * jQuery Validation Plugin v1.14.0
 *
 * http://jqueryvalidation.org/
 *
 * Copyright (c) 2015 JÃ¶rn Zaefferer
 * Released under the MIT license
 */
(function (factory) {
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory);
    } else {
        factory(jQuery);
    }
}(function ($) {

    $.extend($.fn, {
        // http://jqueryvalidation.org/validate/
        validate: function (options) {

            // if nothing is selected, return nothing; can't chain anyway
            if (!this.length) {
                if (options && options.debug && window.console) {
                    console.warn("Nothing selected, can't validate, returning nothing.");
                }
                return;
            }

            // check if a validator for this form was already created
            var validator = $.data(this[0], "validator");
            if (validator) {
                return validator;
            }

            // Add novalidate tag if HTML5.
            this.attr("novalidate", "novalidate");

            validator = new $.validator(options, this[0]);
            $.data(this[0], "validator", validator);

            if (validator.settings.onsubmit) {

                this.on("click.validate", ":submit", function (event) {
                    if (validator.settings.submitHandler) {
                        validator.submitButton = event.target;
                    }

                    // allow suppressing validation by adding a cancel class to the submit button
                    if ($(this).hasClass("cancel")) {
                        validator.cancelSubmit = true;
                    }

                    // allow suppressing validation by adding the html5 formnovalidate attribute to the submit button
                    if ($(this).attr("formnovalidate") !== undefined) {
                        validator.cancelSubmit = true;
                    }
                });

                // validate the form on submit
                this.on("submit.validate", function (event) {
                    if (validator.settings.debug) {
                        // prevent form submit to be able to see console output
                        event.preventDefault();
                    }

                    function handle() {
                        var hidden, result;
                        if (validator.settings.submitHandler) {
                            if (validator.submitButton) {
                                // insert a hidden input as a replacement for the missing submit button
                                hidden = $("<input type='hidden'/>")
                                    .attr("name", validator.submitButton.name)
                                    .val($(validator.submitButton).val())
                                    .appendTo(validator.currentForm);
                            }
                            result = validator.settings.submitHandler.call(validator, validator.currentForm, event);
                            if (validator.submitButton) {
                                // and clean up afterwards; thanks to no-block-scope, hidden can be referenced
                                hidden.remove();
                            }
                            if (result !== undefined) {
                                return result;
                            }
                            return false;
                        }
                        return true;
                    }

                    // prevent submit for invalid forms or custom submit handlers
                    if (validator.cancelSubmit) {
                        validator.cancelSubmit = false;
                        return handle();
                    }
                    if (validator.form()) {
                        if (validator.pendingRequest) {
                            validator.formSubmitted = true;
                            return false;
                        }
                        return handle();
                    } else {
                        validator.focusInvalid();
                        return false;
                    }
                });
            }

            return validator;
        },
        // http://jqueryvalidation.org/valid/
        valid: function () {
            var valid, validator, errorList;

            if ($(this[0]).is("form")) {
                valid = this.validate().form();
            } else {
                errorList = [];
                valid = true;
                validator = $(this[0].form).validate();
                this.each(function () {
                    valid = validator.element(this) && valid;
                    errorList = errorList.concat(validator.errorList);
                });
                validator.errorList = errorList;
            }
            return valid;
        },

        // http://jqueryvalidation.org/rules/
        rules: function (command, argument) {
            var element = this[0],
                settings, staticRules, existingRules, data, param, filtered;

            if (command) {
                settings = $.data(element.form, "validator").settings;
                staticRules = settings.rules;
                existingRules = $.validator.staticRules(element);
                switch (command) {
                    case "add":
                        $.extend(existingRules, $.validator.normalizeRule(argument));
                        // remove messages from rules, but allow them to be set separately
                        delete existingRules.messages;
                        staticRules[element.name] = existingRules;
                        if (argument.messages) {
                            settings.messages[element.name] = $.extend(settings.messages[element.name], argument.messages);
                        }
                        break;
                    case "remove":
                        if (!argument) {
                            delete staticRules[element.name];
                            return existingRules;
                        }
                        filtered = {};
                        $.each(argument.split(/\s/), function (index, method) {
                            filtered[method] = existingRules[method];
                            delete existingRules[method];
                            if (method === "required") {
                                $(element).removeAttr("aria-required");
                            }
                        });
                        return filtered;
                }
            }

            data = $.validator.normalizeRules(
                $.extend({},
                    $.validator.classRules(element),
                    $.validator.attributeRules(element),
                    $.validator.dataRules(element),
                    $.validator.staticRules(element)
                ), element);

            // make sure required is at front
            if (data.required) {
                param = data.required;
                delete data.required;
                data = $.extend({
                    required: param
                }, data);
                $(element).attr("aria-required", "true");
            }

            // make sure remote is at back
            if (data.remote) {
                param = data.remote;
                delete data.remote;
                data = $.extend(data, {
                    remote: param
                });
            }

            return data;
        }
    });

    // Custom selectors
    $.extend($.expr[":"], {
        // http://jqueryvalidation.org/blank-selector/
        blank: function (a) {
            return !$.trim("" + $(a).val());
        },
        // http://jqueryvalidation.org/filled-selector/
        filled: function (a) {
            return !!$.trim("" + $(a).val());
        },
        // http://jqueryvalidation.org/unchecked-selector/
        unchecked: function (a) {
            return !$(a).prop("checked");
        }
    });

    // constructor for validator
    $.validator = function (options, form) {
        this.settings = $.extend(true, {}, $.validator.defaults, options);
        this.currentForm = form;
        this.init();
    };

    // http://jqueryvalidation.org/jQuery.validator.format/
    $.validator.format = function (source, params) {
        if (arguments.length === 1) {
            return function () {
                var args = $.makeArray(arguments);
                args.unshift(source);
                return $.validator.format.apply(this, args);
            };
        }
        if (arguments.length > 2 && params.constructor !== Array) {
            params = $.makeArray(arguments).slice(1);
        }
        if (params.constructor !== Array) {
            params = [params];
        }
        $.each(params, function (i, n) {
            source = source.replace(new RegExp("\\{" + i + "\\}", "g"), function () {
                return n;
            });
        });
        return source;
    };

    $.extend($.validator, {

        defaults: {
            messages: {},
            groups: {},
            rules: {},
            errorClass: "error",
            validClass: "valid",
            errorElement: "label",
            focusCleanup: false,
            focusInvalid: true,
            errorContainer: $([]),
            errorLabelContainer: $([]),
            onsubmit: true,
            ignore: ":hidden",
            ignoreTitle: false,
            onfocusin: function (element) {
                this.lastActive = element;

                // Hide error label and remove error class on focus if enabled
                if (this.settings.focusCleanup) {
                    if (this.settings.unhighlight) {
                        this.settings.unhighlight.call(this, element, this.settings.errorClass, this.settings.validClass);
                    }
                    this.hideThese(this.errorsFor(element));
                }
            },
            onfocusout: function (element) {
                if (!this.checkable(element) && (element.name in this.submitted || !this.optional(element))) {
                    this.element(element);
                }
            },
            onkeyup: function (element, event) {
                // Avoid revalidate the field when pressing one of the following keys
                // Shift       => 16
                // Ctrl        => 17
                // Alt         => 18
                // Caps lock   => 20
                // End         => 35
                // Home        => 36
                // Left arrow  => 37
                // Up arrow    => 38
                // Right arrow => 39
                // Down arrow  => 40
                // Insert      => 45
                // Num lock    => 144
                // AltGr key   => 225
                var excludedKeys = [
                    16, 17, 18, 20, 35, 36, 37,
                    38, 39, 40, 45, 144, 225
                ];

                if (event.which === 9 && this.elementValue(element) === "" || $.inArray(event.keyCode, excludedKeys) !== -1) {
                    return;
                } else if (element.name in this.submitted || element === this.lastElement) {
                    this.element(element);
                }
            },
            onclick: function (element) {
                // click on selects, radiobuttons and checkboxes
                if (element.name in this.submitted) {
                    this.element(element);

                    // or option elements, check parent select in that case
                } else if (element.parentNode.name in this.submitted) {
                    this.element(element.parentNode);
                }
            },
            highlight: function (element, errorClass, validClass) {
                if (element.type === "radio") {
                    this.findByName(element.name).addClass(errorClass).removeClass(validClass);
                } else {
                    $(element).addClass(errorClass).removeClass(validClass);
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                if (element.type === "radio") {
                    this.findByName(element.name).removeClass(errorClass).addClass(validClass);
                } else {
                    $(element).removeClass(errorClass).addClass(validClass);
                }
            }
        },

        // http://jqueryvalidation.org/jQuery.validator.setDefaults/
        setDefaults: function (settings) {
            $.extend($.validator.defaults, settings);
        },

        messages: {
            required: "This field is required.",
            remote: "Please fix this field.",
            email: "Please enter a valid email address.",
            url: "Please enter a valid URL.",
            date: "Please enter a valid date.",
            dateISO: "Please enter a valid date ( ISO ).",
            number: "Please enter a valid number.",
            digits: "Please enter only digits.",
            creditcard: "Please enter a valid credit card number.",
            equalTo: "Please enter the same value again.",
            maxlength: $.validator.format("Please enter no more than {0} characters."),
            minlength: $.validator.format("Please enter at least {0} characters."),
            rangelength: $.validator.format("Please enter a value between {0} and {1} characters long."),
            range: $.validator.format("Please enter a value between {0} and {1}."),
            max: $.validator.format("Please enter a value less than or equal to {0}."),
            min: $.validator.format("Please enter a value greater than or equal to {0}.")
        },

        autoCreateRanges: false,

        prototype: {

            init: function () {
                this.labelContainer = $(this.settings.errorLabelContainer);
                this.errorContext = this.labelContainer.length && this.labelContainer || $(this.currentForm);
                this.containers = $(this.settings.errorContainer).add(this.settings.errorLabelContainer);
                this.submitted = {};
                this.valueCache = {};
                this.pendingRequest = 0;
                this.pending = {};
                this.invalid = {};
                this.reset();

                var groups = (this.groups = {}),
                    rules;
                $.each(this.settings.groups, function (key, value) {
                    if (typeof value === "string") {
                        value = value.split(/\s/);
                    }
                    $.each(value, function (index, name) {
                        groups[name] = key;
                    });
                });
                rules = this.settings.rules;
                $.each(rules, function (key, value) {
                    rules[key] = $.validator.normalizeRule(value);
                });

                function delegate(event) {
                    var validator = $.data(this.form, "validator"),
                        eventType = "on" + event.type.replace(/^validate/, ""),
                        settings = validator.settings;
                    if (settings[eventType] && !$(this).is(settings.ignore)) {
                        settings[eventType].call(validator, this, event);
                    }
                }

                $(this.currentForm)
                    .on("focusin.validate focusout.validate keyup.validate",
                        ":text, [type='password'], [type='file'], select, textarea, [type='number'], [type='search'], " +
                        "[type='tel'], [type='url'], [type='email'], [type='datetime'], [type='date'], [type='month'], " +
                        "[type='week'], [type='time'], [type='datetime-local'], [type='range'], [type='color'], " +
                        "[type='radio'], [type='checkbox']", delegate)
                    // Support: Chrome, oldIE
                    // "select" is provided as event.target when clicking a option
                    .on("click.validate", "select, option, [type='radio'], [type='checkbox']", delegate);

                if (this.settings.invalidHandler) {
                    $(this.currentForm).on("invalid-form.validate", this.settings.invalidHandler);
                }

                // Add aria-required to any Static/Data/Class required fields before first validation
                // Screen readers require this attribute to be present before the initial submission http://www.w3.org/TR/WCAG-TECHS/ARIA2.html
                $(this.currentForm).find("[required], [data-rule-required], .required").attr("aria-required", "true");
            },

            // http://jqueryvalidation.org/Validator.form/
            form: function () {
                this.checkForm();
                $.extend(this.submitted, this.errorMap);
                this.invalid = $.extend({}, this.errorMap);
                if (!this.valid()) {
                    $(this.currentForm).triggerHandler("invalid-form", [this]);
                }
                this.showErrors();
                return this.valid();
            },

            checkForm: function () {
                this.prepareForm();
                for (var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++) {
                    this.check(elements[i]);
                }
                return this.valid();
            },

            // http://jqueryvalidation.org/Validator.element/
            element: function (element) {
                var cleanElement = this.clean(element),
                    checkElement = this.validationTargetFor(cleanElement),
                    result = true;

                this.lastElement = checkElement;

                if (checkElement === undefined) {
                    delete this.invalid[cleanElement.name];
                } else {
                    this.prepareElement(checkElement);
                    this.currentElements = $(checkElement);

                    result = this.check(checkElement) !== false;
                    if (result) {
                        delete this.invalid[checkElement.name];
                    } else {
                        this.invalid[checkElement.name] = true;
                    }
                }
                // Add aria-invalid status for screen readers
                $(element).attr("aria-invalid", !result);

                if (!this.numberOfInvalids()) {
                    // Hide error containers on last error
                    this.toHide = this.toHide.add(this.containers);
                }
                this.showErrors();
                return result;
            },

            // http://jqueryvalidation.org/Validator.showErrors/
            showErrors: function (errors) {
                if (errors) {
                    // add items to error list and map
                    $.extend(this.errorMap, errors);
                    this.errorList = [];
                    for (var name in errors) {
                        this.errorList.push({
                            message: errors[name],
                            element: this.findByName(name)[0]
                        });
                    }
                    // remove items from success list
                    this.successList = $.grep(this.successList, function (element) {
                        return !(element.name in errors);
                    });
                }
                if (this.settings.showErrors) {
                    this.settings.showErrors.call(this, this.errorMap, this.errorList);
                } else {
                    this.defaultShowErrors();
                }
            },

            // http://jqueryvalidation.org/Validator.resetForm/
            resetForm: function () {
                if ($.fn.resetForm) {
                    $(this.currentForm).resetForm();
                }
                this.submitted = {};
                this.lastElement = null;
                this.prepareForm();
                this.hideErrors();
                var i, elements = this.elements()
                    .removeData("previousValue")
                    .removeAttr("aria-invalid");

                if (this.settings.unhighlight) {
                    for (i = 0; elements[i]; i++) {
                        this.settings.unhighlight.call(this, elements[i],
                            this.settings.errorClass, "");
                    }
                } else {
                    elements.removeClass(this.settings.errorClass);
                }
            },

            numberOfInvalids: function () {
                return this.objectLength(this.invalid);
            },

            objectLength: function (obj) {
                /* jshint unused: false */
                var count = 0,
                    i;
                for (i in obj) {
                    count++;
                }
                return count;
            },

            hideErrors: function () {
                this.hideThese(this.toHide);
            },

            hideThese: function (errors) {
                errors.not(this.containers).text("");
                this.addWrapper(errors).hide();
            },

            valid: function () {
                return this.size() === 0;
            },

            size: function () {
                return this.errorList.length;
            },

            focusInvalid: function () {
                if (this.settings.focusInvalid) {
                    try {
                        $(this.findLastActive() || this.errorList.length && this.errorList[0].element || [])
                            .filter(":visible")
                            .focus()
                            // manually trigger focusin event; without it, focusin handler isn't called, findLastActive won't have anything to find
                            .trigger("focusin");
                    } catch (e) {
                        // ignore IE throwing errors when focusing hidden elements
                    }
                }
            },

            findLastActive: function () {
                var lastActive = this.lastActive;
                return lastActive && $.grep(this.errorList, function (n) {
                        return n.element.name === lastActive.name;
                    }).length === 1 && lastActive;
            },

            elements: function () {
                var validator = this,
                    rulesCache = {};

                // select all valid inputs inside the form (no submit or reset buttons)
                return $(this.currentForm)
                    .find("input, select, textarea")
                    .not(":submit, :reset, :image, :disabled")
                    .not(this.settings.ignore)
                    .filter(function () {
                        if (!this.name && validator.settings.debug && window.console) {
                            console.error("%o has no name assigned", this);
                        }

                        // select only the first element for each name, and only those with rules specified
                        if (this.name in rulesCache || !validator.objectLength($(this).rules())) {
                            return false;
                        }

                        rulesCache[this.name] = true;
                        return true;
                    });
            },

            clean: function (selector) {
                return $(selector)[0];
            },

            errors: function () {
                var errorClass = this.settings.errorClass.split(" ").join(".");
                return $(this.settings.errorElement + "." + errorClass, this.errorContext);
            },

            reset: function () {
                this.successList = [];
                this.errorList = [];
                this.errorMap = {};
                this.toShow = $([]);
                this.toHide = $([]);
                this.currentElements = $([]);
            },

            prepareForm: function () {
                this.reset();
                this.toHide = this.errors().add(this.containers);
            },

            prepareElement: function (element) {
                this.reset();
                this.toHide = this.errorsFor(element);
            },

            elementValue: function (element) {
                var val,
                    $element = $(element),
                    type = element.type;

                if (type === "radio" || type === "checkbox") {
                    return this.findByName(element.name).filter(":checked").val();
                } else if (type === "number" && typeof element.validity !== "undefined") {
                    return element.validity.badInput ? false : $element.val();
                }

                val = $element.val();
                if (typeof val === "string") {
                    return val.replace(/\r/g, "");
                }
                return val;
            },

            check: function (element) {
                element = this.validationTargetFor(this.clean(element));

                var rules = $(element).rules(),
                    rulesCount = $.map(rules, function (n, i) {
                        return i;
                    }).length,
                    dependencyMismatch = false,
                    val = this.elementValue(element),
                    result, method, rule;

                for (method in rules) {
                    rule = {
                        method: method,
                        parameters: rules[method]
                    };
                    try {

                        result = $.validator.methods[method].call(this, val, element, rule.parameters);

                        // if a method indicates that the field is optional and therefore valid,
                        // don't mark it as valid when there are no other rules
                        if (result === "dependency-mismatch" && rulesCount === 1) {
                            dependencyMismatch = true;
                            continue;
                        }
                        dependencyMismatch = false;

                        if (result === "pending") {
                            this.toHide = this.toHide.not(this.errorsFor(element));
                            return;
                        }

                        if (!result) {
                            this.formatAndAdd(element, rule);
                            return false;
                        }
                    } catch (e) {
                        if (this.settings.debug && window.console) {
                            console.log("Exception occurred when checking element " + element.id + ", check the '" + rule.method + "' method.", e);
                        }
                        if (e instanceof TypeError) {
                            e.message += ".  Exception occurred when checking element " + element.id + ", check the '" + rule.method + "' method.";
                        }

                        throw e;
                    }
                }
                if (dependencyMismatch) {
                    return;
                }
                if (this.objectLength(rules)) {
                    this.successList.push(element);
                }
                return true;
            },

            // return the custom message for the given element and validation method
            // specified in the element's HTML5 data attribute
            // return the generic message if present and no method specific message is present
            customDataMessage: function (element, method) {
                return $(element).data("msg" + method.charAt(0).toUpperCase() +
                        method.substring(1).toLowerCase()) || $(element).data("msg");
            },

            // return the custom message for the given element name and validation method
            customMessage: function (name, method) {
                var m = this.settings.messages[name];
                return m && (m.constructor === String ? m : m[method]);
            },

            // return the first defined argument, allowing empty strings
            findDefined: function () {
                for (var i = 0; i < arguments.length; i++) {
                    if (arguments[i] !== undefined) {
                        return arguments[i];
                    }
                }
                return undefined;
            },

            defaultMessage: function (element, method) {
                return this.findDefined(
                    this.customMessage(element.name, method),
                    this.customDataMessage(element, method),
                    // title is never undefined, so handle empty string as undefined
                    !this.settings.ignoreTitle && element.title || undefined,
                    $.validator.messages[method],
                    "<strong>Warning: No message defined for " + element.name + "</strong>"
                );
            },

            formatAndAdd: function (element, rule) {
                var message = this.defaultMessage(element, rule.method),
                    theregex = /\$?\{(\d+)\}/g;
                if (typeof message === "function") {
                    message = message.call(this, rule.parameters, element);
                } else if (theregex.test(message)) {
                    message = $.validator.format(message.replace(theregex, "{$1}"), rule.parameters);
                }
                this.errorList.push({
                    message: message,
                    element: element,
                    method: rule.method
                });

                this.errorMap[element.name] = message;
                this.submitted[element.name] = message;
            },

            addWrapper: function (toToggle) {
                if (this.settings.wrapper) {
                    toToggle = toToggle.add(toToggle.parent(this.settings.wrapper));
                }
                return toToggle;
            },

            defaultShowErrors: function () {
                var i, elements, error;
                for (i = 0; this.errorList[i]; i++) {
                    error = this.errorList[i];
                    if (this.settings.highlight) {
                        this.settings.highlight.call(this, error.element, this.settings.errorClass, this.settings.validClass);
                    }
                    this.showLabel(error.element, error.message);
                }
                if (this.errorList.length) {
                    this.toShow = this.toShow.add(this.containers);
                }
                if (this.settings.success) {
                    for (i = 0; this.successList[i]; i++) {
                        this.showLabel(this.successList[i]);
                    }
                }
                if (this.settings.unhighlight) {
                    for (i = 0, elements = this.validElements(); elements[i]; i++) {
                        this.settings.unhighlight.call(this, elements[i], this.settings.errorClass, this.settings.validClass);
                    }
                }
                this.toHide = this.toHide.not(this.toShow);
                this.hideErrors();
                this.addWrapper(this.toShow).show();
            },

            validElements: function () {
                return this.currentElements.not(this.invalidElements());
            },

            invalidElements: function () {
                return $(this.errorList).map(function () {
                    return this.element;
                });
            },

            showLabel: function (element, message) {
                var place, group, errorID,
                    error = this.errorsFor(element),
                    elementID = this.idOrName(element),
                    describedBy = $(element).attr("aria-describedby");
                if (error.length) {
                    // refresh error/success class
                    error.removeClass(this.settings.validClass).addClass(this.settings.errorClass);
                    // replace message on existing label
                    error.html(message);
                } else {
                    // create error element
                    error = $("<" + this.settings.errorElement + ">")
                        .attr("id", elementID + "-error")
                        .addClass(this.settings.errorClass)
                        .html(message || "");

                    // Maintain reference to the element to be placed into the DOM
                    place = error;
                    if (this.settings.wrapper) {
                        // make sure the element is visible, even in IE
                        // actually showing the wrapped element is handled elsewhere
                        place = error.hide().show().wrap("<" + this.settings.wrapper + "/>").parent();
                    }
                    if (this.labelContainer.length) {
                        this.labelContainer.append(place);
                    } else if (this.settings.errorPlacement) {
                        this.settings.errorPlacement(place, $(element));
                    } else {
                        place.insertAfter(element);
                    }

                    // Link error back to the element
                    if (error.is("label")) {
                        // If the error is a label, then associate using 'for'
                        error.attr("for", elementID);
                    } else if (error.parents("label[for='" + elementID + "']").length === 0) {
                        // If the element is not a child of an associated label, then it's necessary
                        // to explicitly apply aria-describedby

                        errorID = error.attr("id").replace(/(:|\.|\[|\]|\$)/g, "\\$1");
                        // Respect existing non-error aria-describedby
                        if (!describedBy) {
                            describedBy = errorID;
                        } else if (!describedBy.match(new RegExp("\\b" + errorID + "\\b"))) {
                            // Add to end of list if not already present
                            describedBy += " " + errorID;
                        }
                        $(element).attr("aria-describedby", describedBy);

                        // If this element is grouped, then assign to all elements in the same group
                        group = this.groups[element.name];
                        if (group) {
                            $.each(this.groups, function (name, testgroup) {
                                if (testgroup === group) {
                                    $("[name='" + name + "']", this.currentForm)
                                        .attr("aria-describedby", error.attr("id"));
                                }
                            });
                        }
                    }
                }
                if (!message && this.settings.success) {
                    error.text("");
                    if (typeof this.settings.success === "string") {
                        error.addClass(this.settings.success);
                    } else {
                        this.settings.success(error, element);
                    }
                }
                this.toShow = this.toShow.add(error);
            },

            errorsFor: function (element) {
                var name = this.idOrName(element),
                    describer = $(element).attr("aria-describedby"),
                    selector = "label[for='" + name + "'], label[for='" + name + "'] *";

                // aria-describedby should directly reference the error element
                if (describer) {
                    selector = selector + ", #" + describer.replace(/\s+/g, ", #");
                }
                return this
                    .errors()
                    .filter(selector);
            },

            idOrName: function (element) {
                return this.groups[element.name] || (this.checkable(element) ? element.name : element.id || element.name);
            },

            validationTargetFor: function (element) {

                // If radio/checkbox, validate first element in group instead
                if (this.checkable(element)) {
                    element = this.findByName(element.name);
                }

                // Always apply ignore filter
                return $(element).not(this.settings.ignore)[0];
            },

            checkable: function (element) {
                return (/radio|checkbox/i).test(element.type);
            },

            findByName: function (name) {
                return $(this.currentForm).find("[name='" + name + "']");
            },

            getLength: function (value, element) {
                switch (element.nodeName.toLowerCase()) {
                    case "select":
                        return $("option:selected", element).length;
                    case "input":
                        if (this.checkable(element)) {
                            return this.findByName(element.name).filter(":checked").length;
                        }
                }
                return value.length;
            },

            depend: function (param, element) {
                return this.dependTypes[typeof param] ? this.dependTypes[typeof param](param, element) : true;
            },

            dependTypes: {
                "boolean": function (param) {
                    return param;
                },
                "string": function (param, element) {
                    return !!$(param, element.form).length;
                },
                "function": function (param, element) {
                    return param(element);
                }
            },

            optional: function (element) {
                var val = this.elementValue(element);
                return !$.validator.methods.required.call(this, val, element) && "dependency-mismatch";
            },

            startRequest: function (element) {
                if (!this.pending[element.name]) {
                    this.pendingRequest++;
                    this.pending[element.name] = true;
                }
            },

            stopRequest: function (element, valid) {
                this.pendingRequest--;
                // sometimes synchronization fails, make sure pendingRequest is never < 0
                if (this.pendingRequest < 0) {
                    this.pendingRequest = 0;
                }
                delete this.pending[element.name];
                if (valid && this.pendingRequest === 0 && this.formSubmitted && this.form()) {
                    $(this.currentForm).submit();
                    this.formSubmitted = false;
                } else if (!valid && this.pendingRequest === 0 && this.formSubmitted) {
                    $(this.currentForm).triggerHandler("invalid-form", [this]);
                    this.formSubmitted = false;
                }
            },

            previousValue: function (element) {
                return $.data(element, "previousValue") || $.data(element, "previousValue", {
                        old: null,
                        valid: true,
                        message: this.defaultMessage(element, "remote")
                    });
            },

            // cleans up all forms and elements, removes validator-specific events
            destroy: function () {
                this.resetForm();

                $(this.currentForm)
                    .off(".validate")
                    .removeData("validator");
            }

        },

        classRuleSettings: {
            required: {
                required: true
            },
            email: {
                email: true
            },
            url: {
                url: true
            },
            date: {
                date: true
            },
            dateISO: {
                dateISO: true
            },
            number: {
                number: true
            },
            digits: {
                digits: true
            },
            creditcard: {
                creditcard: true
            }
        },

        addClassRules: function (className, rules) {
            if (className.constructor === String) {
                this.classRuleSettings[className] = rules;
            } else {
                $.extend(this.classRuleSettings, className);
            }
        },

        classRules: function (element) {
            var rules = {},
                classes = $(element).attr("class");

            if (classes) {
                $.each(classes.split(" "), function () {
                    if (this in $.validator.classRuleSettings) {
                        $.extend(rules, $.validator.classRuleSettings[this]);
                    }
                });
            }
            return rules;
        },

        normalizeAttributeRule: function (rules, type, method, value) {

            // convert the value to a number for number inputs, and for text for backwards compability
            // allows type="date" and others to be compared as strings
            if (/min|max/.test(method) && (type === null || /number|range|text/.test(type))) {
                value = Number(value);

                // Support Opera Mini, which returns NaN for undefined minlength
                if (isNaN(value)) {
                    value = undefined;
                }
            }

            if (value || value === 0) {
                rules[method] = value;
            } else if (type === method && type !== "range") {

                // exception: the jquery validate 'range' method
                // does not test for the html5 'range' type
                rules[method] = true;
            }
        },

        attributeRules: function (element) {
            var rules = {},
                $element = $(element),
                type = element.getAttribute("type"),
                method, value;

            for (method in $.validator.methods) {

                // support for <input required> in both html5 and older browsers
                if (method === "required") {
                    value = element.getAttribute(method);

                    // Some browsers return an empty string for the required attribute
                    // and non-HTML5 browsers might have required="" markup
                    if (value === "") {
                        value = true;
                    }

                    // force non-HTML5 browsers to return bool
                    value = !!value;
                } else {
                    value = $element.attr(method);
                }

                this.normalizeAttributeRule(rules, type, method, value);
            }

            // maxlength may be returned as -1, 2147483647 ( IE ) and 524288 ( safari ) for text inputs
            if (rules.maxlength && /-1|2147483647|524288/.test(rules.maxlength)) {
                delete rules.maxlength;
            }

            return rules;
        },

        dataRules: function (element) {
            var rules = {},
                $element = $(element),
                type = element.getAttribute("type"),
                method, value;

            for (method in $.validator.methods) {
                value = $element.data("rule" + method.charAt(0).toUpperCase() + method.substring(1).toLowerCase());
                this.normalizeAttributeRule(rules, type, method, value);
            }
            return rules;
        },

        staticRules: function (element) {
            var rules = {},
                validator = $.data(element.form, "validator");

            if (validator.settings.rules) {
                rules = $.validator.normalizeRule(validator.settings.rules[element.name]) || {};
            }
            return rules;
        },

        normalizeRules: function (rules, element) {
            // handle dependency check
            $.each(rules, function (prop, val) {
                // ignore rule when param is explicitly false, eg. required:false
                if (val === false) {
                    delete rules[prop];
                    return;
                }
                if (val.param || val.depends) {
                    var keepRule = true;
                    switch (typeof val.depends) {
                        case "string":
                            keepRule = !!$(val.depends, element.form).length;
                            break;
                        case "function":
                            keepRule = val.depends.call(element, element);
                            break;
                    }
                    if (keepRule) {
                        rules[prop] = val.param !== undefined ? val.param : true;
                    } else {
                        delete rules[prop];
                    }
                }
            });

            // evaluate parameters
            $.each(rules, function (rule, parameter) {
                rules[rule] = $.isFunction(parameter) ? parameter(element) : parameter;
            });

            // clean number parameters
            $.each(["minlength", "maxlength"], function () {
                if (rules[this]) {
                    rules[this] = Number(rules[this]);
                }
            });
            $.each(["rangelength", "range"], function () {
                var parts;
                if (rules[this]) {
                    if ($.isArray(rules[this])) {
                        rules[this] = [Number(rules[this][0]), Number(rules[this][1])];
                    } else if (typeof rules[this] === "string") {
                        parts = rules[this].replace(/[\[\]]/g, "").split(/[\s,]+/);
                        rules[this] = [Number(parts[0]), Number(parts[1])];
                    }
                }
            });

            if ($.validator.autoCreateRanges) {
                // auto-create ranges
                if (rules.min != null && rules.max != null) {
                    rules.range = [rules.min, rules.max];
                    delete rules.min;
                    delete rules.max;
                }
                if (rules.minlength != null && rules.maxlength != null) {
                    rules.rangelength = [rules.minlength, rules.maxlength];
                    delete rules.minlength;
                    delete rules.maxlength;
                }
            }

            return rules;
        },

        // Converts a simple string to a {string: true} rule, e.g., "required" to {required:true}
        normalizeRule: function (data) {
            if (typeof data === "string") {
                var transformed = {};
                $.each(data.split(/\s/), function () {
                    transformed[this] = true;
                });
                data = transformed;
            }
            return data;
        },

        // http://jqueryvalidation.org/jQuery.validator.addMethod/
        addMethod: function (name, method, message) {
            $.validator.methods[name] = method;
            $.validator.messages[name] = message !== undefined ? message : $.validator.messages[name];
            if (method.length < 3) {
                $.validator.addClassRules(name, $.validator.normalizeRule(name));
            }
        },

        methods: {

            // http://jqueryvalidation.org/required-method/
            required: function (value, element, param) {
                // check if dependency is met
                if (!this.depend(param, element)) {
                    return "dependency-mismatch";
                }
                if (element.nodeName.toLowerCase() === "select") {
                    // could be an array for select-multiple or a string, both are fine this way
                    var val = $(element).val();
                    return val && val.length > 0;
                }
                if (this.checkable(element)) {
                    return this.getLength(value, element) > 0;
                }
                return value.length > 0;
            },

            // http://jqueryvalidation.org/email-method/
            email: function (value, element) {
                // From https://html.spec.whatwg.org/multipage/forms.html#valid-e-mail-address
                // Retrieved 2014-01-14
                // If you have a problem with this implementation, report a bug against the above spec
                // Or use custom methods to implement your own email validation
                return this.optional(element) || /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(value);
            },

            // http://jqueryvalidation.org/url-method/
            url: function (value, element) {

                // Copyright (c) 2010-2013 Diego Perini, MIT licensed
                // https://gist.github.com/dperini/729294
                // see also https://mathiasbynens.be/demo/url-regex
                // modified to allow protocol-relative URLs
                return this.optional(element) || /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(value);
            },

            // http://jqueryvalidation.org/date-method/
            date: function (value, element) {
                return this.optional(element) || !/Invalid|NaN/.test(new Date(value).toString());
            },

            // http://jqueryvalidation.org/dateISO-method/
            dateISO: function (value, element) {
                return this.optional(element) || /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/.test(value);
            },

            // http://jqueryvalidation.org/number-method/
            number: function (value, element) {
                return this.optional(element) || /^(?:-?\d+|-?\d{1,3}(?:,\d{3})+)?(?:\.\d+)?$/.test(value);
            },

            // http://jqueryvalidation.org/digits-method/
            digits: function (value, element) {
                return this.optional(element) || /^\d+$/.test(value);
            },

            // http://jqueryvalidation.org/creditcard-method/
            // based on http://en.wikipedia.org/wiki/Luhn_algorithm
            creditcard: function (value, element) {
                if (this.optional(element)) {
                    return "dependency-mismatch";
                }
                // accept only spaces, digits and dashes
                if (/[^0-9 \-]+/.test(value)) {
                    return false;
                }
                var nCheck = 0,
                    nDigit = 0,
                    bEven = false,
                    n, cDigit;

                value = value.replace(/\D/g, "");

                // Basing min and max length on
                // http://developer.ean.com/general_info/Valid_Credit_Card_Types
                if (value.length < 13 || value.length > 19) {
                    return false;
                }

                for (n = value.length - 1; n >= 0; n--) {
                    cDigit = value.charAt(n);
                    nDigit = parseInt(cDigit, 10);
                    if (bEven) {
                        if ((nDigit *= 2) > 9) {
                            nDigit -= 9;
                        }
                    }
                    nCheck += nDigit;
                    bEven = !bEven;
                }

                return (nCheck % 10) === 0;
            },

            // http://jqueryvalidation.org/minlength-method/
            minlength: function (value, element, param) {
                var length = $.isArray(value) ? value.length : this.getLength(value, element);
                return this.optional(element) || length >= param;
            },

            // http://jqueryvalidation.org/maxlength-method/
            maxlength: function (value, element, param) {
                var length = $.isArray(value) ? value.length : this.getLength(value, element);
                return this.optional(element) || length <= param;
            },

            // http://jqueryvalidation.org/rangelength-method/
            rangelength: function (value, element, param) {
                var length = $.isArray(value) ? value.length : this.getLength(value, element);
                return this.optional(element) || (length >= param[0] && length <= param[1]);
            },

            // http://jqueryvalidation.org/min-method/
            min: function (value, element, param) {
                return this.optional(element) || value >= param;
            },

            // http://jqueryvalidation.org/max-method/
            max: function (value, element, param) {
                return this.optional(element) || value <= param;
            },

            // http://jqueryvalidation.org/range-method/
            range: function (value, element, param) {
                return this.optional(element) || (value >= param[0] && value <= param[1]);
            },

            // http://jqueryvalidation.org/equalTo-method/
            equalTo: function (value, element, param) {
                // bind to the blur event of the target in order to revalidate whenever the target field is updated
                // TODO find a way to bind the event just once, avoiding the unbind-rebind overhead
                var target = $(param);
                if (this.settings.onfocusout) {
                    target.off(".validate-equalTo").on("blur.validate-equalTo", function () {
                        $(element).valid();
                    });
                }
                return value === target.val();
            },

            // http://jqueryvalidation.org/remote-method/
            remote: function (value, element, param) {
                if (this.optional(element)) {
                    return "dependency-mismatch";
                }

                var previous = this.previousValue(element),
                    validator, data;

                if (!this.settings.messages[element.name]) {
                    this.settings.messages[element.name] = {};
                }
                previous.originalMessage = this.settings.messages[element.name].remote;
                this.settings.messages[element.name].remote = previous.message;

                param = typeof param === "string" && {
                        url: param
                    } || param;

                if (previous.old === value) {
                    return previous.valid;
                }

                previous.old = value;
                validator = this;
                this.startRequest(element);
                data = {};
                data[element.name] = value;
                $.ajax($.extend(true, {
                    mode: "abort",
                    port: "validate" + element.name,
                    dataType: "json",
                    data: data,
                    context: validator.currentForm,
                    success: function (response) {
                        var valid = response === true || response === "true",
                            errors, message, submitted;

                        validator.settings.messages[element.name].remote = previous.originalMessage;
                        if (valid) {
                            submitted = validator.formSubmitted;
                            validator.prepareElement(element);
                            validator.formSubmitted = submitted;
                            validator.successList.push(element);
                            delete validator.invalid[element.name];
                            validator.showErrors();
                        } else {
                            errors = {};
                            message = response || validator.defaultMessage(element, "remote");
                            errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message;
                            validator.invalid[element.name] = true;
                            validator.showErrors(errors);
                        }
                        previous.valid = valid;
                        validator.stopRequest(element, valid);
                    }
                }, param));
                return "pending";
            }
        }

    });

    // ajax mode: abort
    // usage: $.ajax({ mode: "abort"[, port: "uniqueport"]});
    // if mode:"abort" is used, the previous request on that port (port can be undefined) is aborted via XMLHttpRequest.abort()

    var pendingRequests = {},
        ajax;
    // Use a prefilter if available (1.5+)
    if ($.ajaxPrefilter) {
        $.ajaxPrefilter(function (settings, _, xhr) {
            var port = settings.port;
            if (settings.mode === "abort") {
                if (pendingRequests[port]) {
                    pendingRequests[port].abort();
                }
                pendingRequests[port] = xhr;
            }
        });
    } else {
        // Proxy ajax
        ajax = $.ajax;
        $.ajax = function (settings) {
            var mode = ("mode" in settings ? settings : $.ajaxSettings).mode,
                port = ("port" in settings ? settings : $.ajaxSettings).port;
            if (mode === "abort") {
                if (pendingRequests[port]) {
                    pendingRequests[port].abort();
                }
                pendingRequests[port] = ajax.apply(this, arguments);
                return pendingRequests[port];
            }
            return ajax.apply(this, arguments);
        };
    }

}));
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
(function (name, definition) {
    if (typeof module !== "undefined") {
        module.exports = definition();
    } else if (typeof define === "function" && typeof define.amd === "object") {
        define(definition);
    } else {
        this[name] = definition();
    }
}("clipboard", function () {
    var clipboard = {};

    clipboard.copy = (function () {
        var _intercept = false;
        var _data = null; // Map from data type (e.g. "text/html") to value.

        function cleanup() {
            _intercept = false;
            _data = null;
        }

        document.addEventListener("copy", function (e) {
            if (_intercept) {
                for (var key in _data) {
                    e.clipboardData.setData(key, _data[key]);
                }
                e.preventDefault();
            }
        });

        return function (data) {
            return new Promise(function (resolve, reject) {
                _intercept = true;
                if (typeof data === "string") {
                    _data = {
                        "text/plain": data
                    };
                } else if (data instanceof Node) {
                    _data = {
                        "text/html": new XMLSerializer().serializeToString(data)
                    };
                } else {
                    _data = data;
                }
                try {
                    if (document.execCommand("copy")) {
                        // document.execCommand is synchronous: http://www.w3.org/TR/2015/WD-clipboard-apis-20150421/#integration-with-rich-text-editing-apis
                        // So we can call resolve() back here.
                        cleanup();
                        resolve();
                    } else {
                        throw new Error("Unable to copy. Perhaps it's not available in your browser?");
                    }
                } catch (e) {
                    cleanup();
                    reject(e);
                }
            });
        };
    })();

    clipboard.paste = (function () {
        var _intercept = false;
        var _resolve;
        var _dataType;

        document.addEventListener("paste", function (e) {
            if (_intercept) {
                _intercept = false;
                e.preventDefault();
                var resolve = _resolve;
                _resolve = null;
                resolve(e.clipboardData.getData(_dataType));
            }
        });

        return function (dataType) {
            return new Promise(function (resolve, reject) {
                _intercept = true;
                _resolve = resolve;
                _dataType = dataType || "text/plain";
                try {
                    if (!document.execCommand("paste")) {
                        _intercept = false;
                        reject(new Error("Unable to paste. Pasting only works in Internet Explorer at the moment."));
                    }
                } catch (e) {
                    _intercept = false;
                    reject(new Error(e));
                }
            });
        };
    })();

    // Handle IE behaviour.
    if (typeof ClipboardEvent === "undefined" &&
        typeof window.clipboardData !== "undefined" &&
        typeof window.clipboardData.setData !== "undefined") {

        /*! promise-polyfill 2.0.1 */
        (function (a) {
            function b(a, b) {
                return function () {
                    a.apply(b, arguments)
                }
            }

            function c(a) {
                if ("object" != typeof this) throw new TypeError("Promises must be constructed via new");
                if ("function" != typeof a) throw new TypeError("not a function");
                this._state = null, this._value = null, this._deferreds = [], i(a, b(e, this), b(f, this))
            }

            function d(a) {
                var b = this;
                return null === this._state ? void this._deferreds.push(a) : void j(function () {
                    var c = b._state ? a.onFulfilled : a.onRejected;
                    if (null === c) return void(b._state ? a.resolve : a.reject)(b._value);
                    var d;
                    try {
                        d = c(b._value)
                    } catch (e) {
                        return void a.reject(e)
                    }
                    a.resolve(d)
                })
            }

            function e(a) {
                try {
                    if (a === this) throw new TypeError("A promise cannot be resolved with itself.");
                    if (a && ("object" == typeof a || "function" == typeof a)) {
                        var c = a.then;
                        if ("function" == typeof c) return void i(b(c, a), b(e, this), b(f, this))
                    }
                    this._state = !0, this._value = a, g.call(this)
                } catch (d) {
                    f.call(this, d)
                }
            }

            function f(a) {
                this._state = !1, this._value = a, g.call(this)
            }

            function g() {
                for (var a = 0, b = this._deferreds.length; b > a; a++) d.call(this, this._deferreds[a]);
                this._deferreds = null
            }

            function h(a, b, c, d) {
                this.onFulfilled = "function" == typeof a ? a : null, this.onRejected = "function" == typeof b ? b : null, this.resolve = c, this.reject = d
            }

            function i(a, b, c) {
                var d = !1;
                try {
                    a(function (a) {
                        d || (d = !0, b(a))
                    }, function (a) {
                        d || (d = !0, c(a))
                    })
                } catch (e) {
                    if (d) return;
                    d = !0, c(e)
                }
            }

            var j = c.immediateFn || "function" == typeof setImmediate && setImmediate || function (a) {
                        setTimeout(a, 1)
                    },
                k = Array.isArray || function (a) {
                        return "[object Array]" === Object.prototype.toString.call(a)
                    };
            c.prototype["catch"] = function (a) {
                return this.then(null, a)
            }, c.prototype.then = function (a, b) {
                var e = this;
                return new c(function (c, f) {
                    d.call(e, new h(a, b, c, f))
                })
            }, c.all = function () {
                var a = Array.prototype.slice.call(1 === arguments.length && k(arguments[0]) ? arguments[0] : arguments);
                return new c(function (b, c) {
                    function d(f, g) {
                        try {
                            if (g && ("object" == typeof g || "function" == typeof g)) {
                                var h = g.then;
                                if ("function" == typeof h) return void h.call(g, function (a) {
                                    d(f, a)
                                }, c)
                            }
                            a[f] = g, 0 === --e && b(a)
                        } catch (i) {
                            c(i)
                        }
                    }

                    if (0 === a.length) return b([]);
                    for (var e = a.length, f = 0; f < a.length; f++) d(f, a[f])
                })
            }, c.resolve = function (a) {
                return a && "object" == typeof a && a.constructor === c ? a : new c(function (b) {
                    b(a)
                })
            }, c.reject = function (a) {
                return new c(function (b, c) {
                    c(a)
                })
            }, c.race = function (a) {
                return new c(function (b, c) {
                    for (var d = 0, e = a.length; e > d; d++) a[d].then(b, c)
                })
            }, "undefined" != typeof module && module.exports ? module.exports = c : a.Promise || (a.Promise = c)
        })(this);

        clipboard.copy = function (data) {
            return new Promise(function (resolve, reject) {
                // IE supports string and URL types: https://msdn.microsoft.com/en-us/library/ms536744(v=vs.85).aspx
                // We only support the string type for now.
                if (typeof data !== "string" && !("text/plain" in data)) {
                    throw new Error("You must provide a text/plain type.");
                }

                var strData = (typeof data === "string" ? data : data["text/plain"]);
                var copySucceeded = window.clipboardData.setData("Text", strData);
                if (copySucceeded) {
                    resolve();
                } else {
                    reject(new Error("Copying was rejected."));
                }
            });
        };

        clipboard.paste = function () {
            return new Promise(function (resolve, reject) {
                var strData = window.clipboardData.getData("Text");
                if (strData) {
                    resolve(strData);
                } else {
                    // The user rejected the paste request.
                    reject(new Error("Pasting was rejected."));
                }
            });
        };
    }

    return clipboard;
}));
/*!
 * jquery.confirm
 *
 * @version 2.3.1
 *
 * @author My C-Labs
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 * @author Russel Vela
 * @author Marcus Schwarz <msspamfang@gmx.de>
 *
 * @license MIT
 * @url https://myclabs.github.io/jquery.confirm/
 */
(function ($) {

    /**
     * Confirm a link or a button
     * @param [options] {{title, text, confirm, cancel, confirmButton, cancelButton, post, confirmButtonClass}}
     */
    $.fn.confirm = function (options) {
        if (typeof options === 'undefined') {
            options = {};
        }

        this.click(function (e) {
            e.preventDefault();

            var newOptions = $.extend({
                button: $(this)
            }, options);

            $.confirm(newOptions, e);
        });

        return this;
    };

    /**
     * Show a confirmation dialog
     * @param [options] {{title, text, confirm, cancel, confirmButton, cancelButton, post, confirmButtonClass}}
     * @param [e] {Event}
     */
    $.confirm = function (options, e) {
        // Do nothing when active confirm modal.
        if ($('.confirmation-modal').length > 0)
            return;

        // Parse options defined with "data-" attributes
        var dataOptions = {};
        if (options.button) {
            var dataOptionsMapping = {
                'title': 'title',
                'text': 'text',
                'confirm-button': 'confirmButton',
                'cancel-button': 'cancelButton',
                'confirm-button-class': 'confirmButtonClass',
                'cancel-button-class': 'cancelButtonClass',
                'dialog-class': 'dialogClass'
            };
            $.each(dataOptionsMapping, function (attributeName, optionName) {
                var value = options.button.data(attributeName);
                if (value) {
                    dataOptions[optionName] = value;
                }
            });
        }

        // Default options
        var settings = $.extend({}, $.confirm.options, {
            confirm: function () {
                var url = e && (('string' === typeof e && e) || (e.currentTarget && e.currentTarget.attributes['href'].value));
                if (url) {
                    if (options.post) {
                        var form = $('<form method="post" class="hide" action="' + url + '"></form>');
                        $("body").append(form);
                        form.submit();
                    } else {
                        window.location = url;
                    }
                }
            },
            cancel: function (o) {
            },
            button: null
        }, dataOptions, options);

        // Modal
        var modalHeader = '';
        var modalHTML = '<div class="confirm_box ' + settings.dialogClass + '">' +
            '<div class="overlay"></div>' +
            '<div class="confirm_model">' +
            '<div class="model">' +
            '<div class="header">' +
            '<h1 class="title">' + settings.title + '</h1>' +
            ' </div>' +
            ' <div class="content">' +
            '<p class="text">' + settings.text + '</p>' +
            '<div class="buttons_container">' +
            '<button class="confirm button ' + settings.confirmButtonClass + '">' + settings.confirmButton + '</button>' +
            '<button class="cancel deny button ' + settings.cancelButtonClass + '">' + settings.cancelButton + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';

        var modal = $(modalHTML);

        modal.on('shown.bs.modal', function () {
            modal.find(".btn-primary:first").focus();
            $('.confirm_box').remove();
        });
        modal.on('hidden.bs.modal', function () {
            $('.confirm_box').remove();
        });
        modal.find(".confirm").click(function () {
            settings.confirm(settings.button);
            $('.confirm_box').remove();
        });
        modal.find(".cancel").click(function () {
            settings.cancel(settings.button);
            $('.confirm_box').remove();
        });

        // Show the modal
        $("body").append(modal);
        modal.show();
    };

    /**
     * Globally definable rules
     */
    $.confirm.options = {
        text: tran.Are_you_sure.trans,
        title: "",
        confirmButton: tran.Yes.trans,
        cancelButton: tran.No.trans,
        post: false,
        confirmButtonClass: "btn-primary",
        cancelButtonClass: "btn-default",
        dialogClass: "modal-dialog"
    }
})(jQuery);


function getCookie(name) {
    var cookieValue = null;
    if (document.cookie && document.cookie != '') {
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = jQuery.trim(cookies[i]);
            // Does this cookie string begin with the name we want?
            if (cookie.substring(0, name.length + 1) == (name + '=')) {
                cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                break;
            }
        }
    }
    return cookieValue;
}
setInterval(function () {
    Buckty.csrfTokenUpdate();
}, 1000 * 60 * 1);
(function ($) {
    if ($.fn.ajaxForm == undefined) {
        $.getScript(site_url + 'assets/js/jquery.form.js');
    }
    var feature = {};
    feature.fileapi = $("<input  type='file'/>").get(0).files !== undefined;
    feature.formdata = window.FormData !== undefined;
    $.fn.uploadFile = function (options) {
        // This is the easiest way to have default options.
        var s = $.extend({
            // These are the defaults.
            url: "",
            method: "POST",
            enctype: "multipart/form-data",
            returnType: null,
            allowDuplicates: true,
            duplicateStrict: false,
            allowedTypes: "*",
            acceptFiles: "*",
            fileName: "file",
            formData: false,
            dynamicFormData: false,
            maxFileSize: -1,
            maxFileCount: -1,
            multiple: true,
            dragDrop: true,
            autoSubmit: true,
            showCancel: true,
            showAbort: true,
            showDone: false,
            showDelete: false,
            showError: true,
            showStatusAfterSuccess: true,
            showStatusAfterError: false,
            showFileCounter: false,
            fileCounterStyle: "). ",
            showFileSize: true,
            showProgress: true,
            nestedForms: true,
            showDownload: false,
            onLoad: function (obj) {
            },
            onSelect: function (files) {
                return true;
            },
            onSubmit: function (files, xhr) {
            },
            onSuccess: function (files, response, xhr, pd) {
            },
            onError: function (files, status, message, pd) {
            },
            onCancel: function (files, pd) {
            },
            onAbort: function (files, pd) {
            },
            downloadCallback: false,
            deleteCallback: false,
            afterUploadAll: false,
            serialize: true,
            sequential: false,
            sequentialCount: 2,
            customProgressBar: false,
            abortButtonClass: "ajax-file-upload-abort",
            cancelButtonClass: "ajax-file-upload-cancel",
            dragDropContainerClass: "ajax-upload-dragdrop",
            dragDropHoverClass: "state-hover",
            errorClass: "ajax-file-upload-error",
            uploadButtonClass: "ajax-file-upload",
            dragDropStr: '<div class="dropper_main"><i class="fa fa-cloud-upload"></i></div>',
            uploadStr: "",
            abortStr: "<i class='fa fa-close'></i>",
            cancelStr: "Cancel",
            deletelStr: "Delete",
            doneStr: "Done",
            multiDragErrorStr: "Multiple File Drag &amp; Drop is not allowed.",
            extErrorStr: "is not allowed. Allowed extensions: ",
            duplicateErrorStr: "is not allowed. File already exists.",
            sizeErrorStr: "is not allowed. Allowed Max size: ",
            uploadErrorStr: "Upload is not allowed",
            maxFileCountErrorStr: " is not allowed. Maximum allowed files are:",
            downloadStr: "Download",
            customErrorKeyStr: "jquery-upload-file-error",
            showQueueDiv: false,
            statusBarWidth: 400,
            dragdropWidth: 400,
            showPreview: true,
            previewHeight: "auto",
            previewWidth: "100%",
            extraHTML: false,
            uploadQueueOrder: 'top'
        }, options);

        this.fileCounter = 1;
        this.selectedFiles = 0;
        var formGroup = "ajax-file-upload-" + (new Date().getTime());
        this.formGroup = formGroup;
        this.errorLog = $("<div></div>"); //Writing errors
        this.responses = [];
        this.existingFileNames = [];
        if (!feature.formdata) //check drag drop enabled.
        {
            s.dragDrop = false;
        }
        if (!feature.formdata) {
            s.multiple = false;
        }

        $(this).html("");

        var obj = this;

        var uploadLabel = $('<div>' + s.uploadStr + '</div>');

        $(uploadLabel).addClass(s.uploadButtonClass);

        // wait form ajax Form plugin and initialize
        (function checkAjaxFormLoaded() {
            if ($.fn.ajaxForm) {

                if (s.dragDrop) {
                    var dragDrop = $('<div class="' + s.dragDropContainerClass + '" style="vertical-align:top;"></div>').width(s.dragdropWidth);
                    $(obj).append(dragDrop);
                    $(dragDrop).append(uploadLabel);
                    $(dragDrop).append($(s.dragDropStr));
                    setDragDropHandlers(obj, s, dragDrop);

                } else {
                    $(obj).append(uploadLabel);
                }
                $(obj).append(obj.errorLog);

                if (s.showQueueDiv)
                    obj.container = $("#" + s.showQueueDiv);
                else
                    obj.container = $('.uploading_queue');

                s.onLoad.call(this, obj);
                createCustomInputFile(obj, formGroup, s, uploadLabel);

            } else window.setTimeout(checkAjaxFormLoaded, 10);
        })();

        this.startUpload = function () {
            $("form").each(function (i, items) {
                if ($(this).hasClass(obj.formGroup)) {
                    mainQ.push($(this));
                }
            });

            if (mainQ.length >= 1)
                submitPendingUploads();

        }

        this.getFileCount = function () {
            return obj.selectedFiles;

        }
        this.stopUpload = function () {
            $("." + s.abortButtonClass).each(function (i, items) {
                if ($(this).hasClass(obj.formGroup)) $(this).click();
            });
            $("." + s.cancelButtonClass).each(function (i, items) {
                if ($(this).hasClass(obj.formGroup)) $(this).click();
            });
        }
        this.cancelAll = function () {
            $("." + s.cancelButtonClass).each(function (i, items) {
                if ($(this).hasClass(obj.formGroup)) $(this).click();
            });
        }
        this.update = function (settings) {
            //update new settings
            s = $.extend(s, settings);
        }
        this.reset = function (removeStatusBars) {
            obj.fileCounter = 1;
            obj.selectedFiles = 0;
            obj.errorLog.html("");
            //remove all the status bars.
            if (removeStatusBars != false) {
                obj.container.html("");
            }
        }
        this.remove = function () {
            obj.container.html("");
            $(obj).remove();

        }
        //This is for showing Old files to user.
        this.createProgress = function (filename, filepath, filesize) {
            var pd = new createProgressDiv(this, s);
            pd.progressDiv.show();
            pd.progressbar.width('100%');

            var fileNameStr = "";
            if (s.showFileCounter)
                fileNameStr = obj.fileCounter + s.fileCounterStyle + filename;
            else fileNameStr = filename;


            if (s.showFileSize)
                fileNameStr += " (" + getSizeStr(filesize) + ")";


            pd.filename.html(fileNameStr);
            obj.fileCounter++;
            obj.selectedFiles++;
            if (s.showPreview) {
                pd.preview.attr('src', filepath);
                pd.preview.show();
            }

            if (s.showDownload) {
                pd.download.show();
                pd.download.click(function () {
                    if (s.downloadCallback) s.downloadCallback.call(obj, [filename]);
                });
            }
            if (s.showDelete) {
                pd.del.show();
                pd.del.click(function () {
                    pd.statusbar.hide().remove();
                    var arr = [filename];
                    if (s.deleteCallback) s.deleteCallback.call(this, arr, pd);
                    obj.selectedFiles -= 1;
                    updateFileCounter(s, obj);
                });
            }

            return pd;
        }

        this.getResponses = function () {
            return this.responses;
        }
        var mainQ = [];
        var progressQ = []
        var running = false;

        function submitPendingUploads() {
            if (running) return;
            running = true;
            (function checkPendingForms() {

                //if not sequential upload all files
                if (!s.sequential) s.sequentialCount = 99999;

                if (mainQ.length == 0 && progressQ.length == 0) {
                    if (s.afterUploadAll) s.afterUploadAll(obj);
                    running = false;
                }
                else {
                    if (progressQ.length < s.sequentialCount) {
                        var frm = mainQ.shift();
                        if (frm != undefined) {
                            progressQ.push(frm);
                            //Remove the class group.
                            frm.removeClass(obj.formGroup);
                            frm.submit();
                        }
                    }
                    window.setTimeout(checkPendingForms, 100);
                }
            })();
        }

        function setDragDropHandlers(obj, s, ddObj) {
            ddObj.on('dragenter', function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).addClass(s.dragDropHoverClass);
            });
            $('.uploading_queue').on('dragover', function (e) {
                e.stopPropagation();
                e.preventDefault();
                var that = $(this);
                if (that.hasClass(s.dragDropContainerClass) && !that.hasClass(s.dragDropHoverClass)) {
                    that.addClass(s.dragDropHoverClass);
                }
            });
            $('.uploading_queue').on('drop', function (e) {
                e.preventDefault();
                $(this).removeClass(s.dragDropHoverClass);
                obj.errorLog.html("");
                var files = e.originalEvent.dataTransfer.files;
                if (!s.multiple && files.length > 1) {
                    if (s.showError) $("<div class='" + s.errorClass + "'>" + s.multiDragErrorStr + "</div>").appendTo(obj.errorLog);
                    return;
                }
                if (s.onSelect(files) == false) return;
                serializeAndUploadFiles(s, obj, files);
            });
            ddObj.on('dragleave', function (e) {
                $(this).removeClass(s.dragDropHoverClass);
            });

            $(document).on('dragenter', function (e) {
                e.stopPropagation();
                e.preventDefault();
                Buckty.uploaderQueue();
            });
            $(document).on('dragover', function (e) {
                e.stopPropagation();
                e.preventDefault();
                var that = $(this);
                if (!that.hasClass(s.dragDropContainerClass)) {
                    that.removeClass(s.dragDropHoverClass);
                }
            });
            $(document).on('drop', function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).removeClass(s.dragDropHoverClass);
            });

        }

        function getSizeStr(size) {
            var sizeStr = "";
            var sizeKB = size / 1024;
            if (parseInt(sizeKB) > 1024) {
                var sizeMB = sizeKB / 1024;
                sizeStr = sizeMB.toFixed(2) + " MB";
            } else {
                sizeStr = sizeKB.toFixed(2) + " KB";
            }
            return sizeStr;
        }

        function serializeData(extraData) {
            var serialized = [];
            if (jQuery.type(extraData) == "string") {
                serialized = extraData.split('&');
            } else {
                serialized = $.param(extraData).split('&');
            }
            var len = serialized.length;
            var result = [];
            var i, part;
            for (i = 0; i < len; i++) {
                serialized[i] = serialized[i].replace(/\+/g, ' ');
                part = serialized[i].split('=');
                result.push([decodeURIComponent(part[0]), decodeURIComponent(part[1])]);
            }
            return result;
        }

        function noserializeAndUploadFiles(s, obj, files) {
            var ts = s;
            var fd = new FormData();
            var fileArray = [];
            var fileName = s.fileName.replace("[]", "");
            var fileListStr = "";

            for (var i = 0; i < files.length; i++) {
                if (!isFileTypeAllowed(obj, s, files[i].name)) {
                    if (s.showError) $("<div><font color='red'><b>" + files[i].name + "</b> " + s.extErrorStr + s.allowedTypes + "</font></div>").appendTo(obj.errorLog);
                    continue;
                }
                if (s.maxFileSize != -1 && files[i].size > s.maxFileSize) {
                    if (s.showError) $("<div><font color='red'><b>" + files[i].name + "</b> " + s.sizeErrorStr + getSizeStr(s.maxFileSize) + "</font></div>").appendTo(obj.errorLog);
                    continue;
                }
                fd.append(fileName + "[]", files[i]);
                fileArray.push(files[i].name);
                fileListStr += obj.fileCounter + "). " + files[i].name + "<br>";
                obj.fileCounter++;
            }
            if (fileArray.length == 0) return;

            var extraData = s.formData;
            if (extraData) {
                var sData = serializeData(extraData);
                for (var j = 0; j < sData.length; j++) {
                    if (sData[j]) {
                        fd.append(sData[j][0], sData[j][1]);
                    }
                }
            }


            ts.fileData = fd;
            var pd = new createProgressDiv(obj, s);
            pd.filename.html(fileListStr);
            var form = $("<form style='display:block; position:absolute;left: 150px;' class='" + obj.formGroup + "' method='" + s.method + "' action='" + s.url + "' enctype='" + s.enctype + "'></form>");
            form.appendTo('body');
            ajaxFormSubmit(form, ts, pd, fileArray, obj);

        }


        function serializeAndUploadFiles(s, obj, files) {
            for (var i = 0; i < files.length; i++) {
                if (!isFileTypeAllowed(obj, s, files[i].name)) {
                    if (s.showError) $("<div class='" + s.errorClass + "'><b>" + files[i].name + "</b> " + s.extErrorStr + s.allowedTypes + "</div>").appendTo(obj.errorLog);
                    continue;
                }
                if (!s.allowDuplicates && isFileDuplicate(obj, files[i].name)) {
                    if (s.showError) $("<div class='" + s.errorClass + "'><b>" + files[i].name + "</b> " + s.duplicateErrorStr + "</div>").appendTo(obj.errorLog);
                    continue;
                }
                if (s.maxFileSize != -1 && files[i].size > s.maxFileSize) {
                    if (s.showError) $("<div class='" + s.errorClass + "'><b>" + files[i].name + "</b> " + s.sizeErrorStr + getSizeStr(s.maxFileSize) + "</div>").appendTo(
                        obj.errorLog);
                    continue;
                }
                if (s.maxFileCount != -1 && obj.selectedFiles >= s.maxFileCount) {
                    if (s.showError) $("<div class='" + s.errorClass + "'><b>" + files[i].name + "</b> " + s.maxFileCountErrorStr + s.maxFileCount + "</div>").appendTo(
                        obj.errorLog);
                    continue;
                }
                obj.selectedFiles++;
                obj.existingFileNames.push(files[i].name);
                var ts = s;
                var fd = new FormData();
                var fileName = s.fileName.replace("[]", "");
                fd.append(fileName, files[i]);
                fd.append('f_in', Buckty.getCurrentin());
                fd.append('csrf_Buckty', Buckty.Buckty_getToken());
                var extraData = s.formData;
                if (extraData) {
                    var sData = serializeData(extraData);
                    for (var j = 0; j < sData.length; j++) {
                        if (sData[j]) {
                            fd.append(sData[j][0], sData[j][1]);
                        }
                    }
                }
                ts.fileData = fd;

                var pd = new createProgressDiv(obj, s);
                var fileNameStr = "";
                if (s.showFileCounter) fileNameStr = obj.fileCounter + s.fileCounterStyle + files[i].name
                else fileNameStr = files[i].name;

                if (s.showFileSize)
                    fileNameStr += "<span class='file_size'>" + getSizeStr(files[i].size) + "</span>";

                pd.filename.html(fileNameStr);
                var form = $("<form style='display:block; position:absolute;left: 150px;' class='" + obj.formGroup + "' method='" + s.method + "' action='" +
                    s.url + "' enctype='" + s.enctype + "'></form>");
                form.appendTo('body');
                var fileArray = [];
                fileArray.push(files[i].name);

                ajaxFormSubmit(form, ts, pd, fileArray, obj, files[i]);
                obj.fileCounter++;
            }
        }

        function isFileTypeAllowed(obj, s, fileName) {
            var fileExtensions = s.allowedTypes.toLowerCase().split(/[\s,]+/g);
            var ext = fileName.split('.').pop().toLowerCase();
            if (s.allowedTypes != "*" && jQuery.inArray(ext, fileExtensions) < 0) {
                return false;
            }
            return true;
        }

        function isFileDuplicate(obj, filename) {
            var duplicate = false;
            if (obj.existingFileNames.length) {
                for (var x = 0; x < obj.existingFileNames.length; x++) {
                    if (obj.existingFileNames[x] == filename
                        || s.duplicateStrict && obj.existingFileNames[x].toLowerCase() == filename.toLowerCase()
                    ) {
                        duplicate = true;
                    }
                }
            }
            return duplicate;
        }

        function removeExistingFileName(obj, fileArr) {
            if (obj.existingFileNames.length) {
                for (var x = 0; x < fileArr.length; x++) {
                    var pos = obj.existingFileNames.indexOf(fileArr[x]);
                    if (pos != -1) {
                        obj.existingFileNames.splice(pos, 1);
                    }
                }
            }
        }

        function getSrcToPreview(file, obj) {
            if (file) {
                obj.show();
                var reader = new FileReader();
                reader.onload = function (e) {
                    obj.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }

        function updateFileCounter(s, obj) {
            if (s.showFileCounter) {
                var count = $(obj.container).find(".ajax-file-upload-filename").length;
                obj.fileCounter = count + 1;
                $(obj.container).find(".ajax-file-upload-filename").each(function (i, items) {
                    var arr = $(this).html().split(s.fileCounterStyle);
                    var fileNum = parseInt(arr[0]) - 1; //decrement;
                    var name = count + s.fileCounterStyle + arr[1];
                    $(this).html(name);
                    count--;
                });
            }
        }

        function createCustomInputFile(obj, group, s, uploadLabel) {

            var fileUploadId = 'ajax-upload-select';

            var form = $("<form method='" + s.method + "' action='" + s.url + "' enctype='" + s.enctype + "'></form>");
            var fileInputStr = "<input type='file' id='" + fileUploadId + "' name='" + s.fileName + "' accept='" + s.acceptFiles + "'/>";
            if (s.multiple) {
                if (s.fileName.indexOf("[]") != s.fileName.length - 2) // if it does not endwith
                {
                    s.fileName += "[]";
                }
                fileInputStr = "<input type='file' id='" + fileUploadId + "' name='" + s.fileName + "' accept='" + s.acceptFiles + "' multiple/>";
            }
            var fileInput = $(fileInputStr).appendTo(form);
            fileInput.change(function () {

                obj.errorLog.html("");
                var fileExtensions = s.allowedTypes.toLowerCase().split(",");
                var fileArray = [];
                if (this.files) //support reading files
                {
                    for (i = 0; i < this.files.length; i++) {
                        fileArray.push(this.files[i].name);
                    }

                    if (s.onSelect(this.files) == false) return;
                } else {
                    var filenameStr = $(this).val();
                    var flist = [];
                    fileArray.push(filenameStr);
                    if (!isFileTypeAllowed(obj, s, filenameStr)) {
                        if (s.showError) $("<div class='" + s.errorClass + "'><b>" + filenameStr + "</b> " + s.extErrorStr + s.allowedTypes + "</div>").appendTo(
                            obj.errorLog);
                        return;
                    }
                    //fallback for browser without FileAPI
                    flist.push({
                        name: filenameStr,
                        size: 'NA'
                    });
                    if (s.onSelect(flist) == false) return;

                }
                updateFileCounter(s, obj);

                uploadLabel.unbind("click");
                form.hide();
                createCustomInputFile(obj, group, s, uploadLabel);
                form.addClass(group);
                if (s.serialize && feature.fileapi && feature.formdata) //use HTML5 support and split file submission
                {
                    form.removeClass(group); //Stop Submitting when.
                    var files = this.files;
                    form.remove();
                    serializeAndUploadFiles(s, obj, files);
                } else {
                    var fileList = "";
                    for (var i = 0; i < fileArray.length; i++) {
                        if (s.showFileCounter) fileList += obj.fileCounter + s.fileCounterStyle + fileArray[i] + "<br>";
                        else fileList += fileArray[i] + "<br>";
                        ;
                        obj.fileCounter++;

                    }
                    if (s.maxFileCount != -1 && (obj.selectedFiles + fileArray.length) > s.maxFileCount) {
                        if (s.showError) $("<div class='" + s.errorClass + "'><b>" + fileList + "</b> " + s.maxFileCountErrorStr + s.maxFileCount + "</div>").appendTo(
                            obj.errorLog);
                        return;
                    }
                    obj.selectedFiles += fileArray.length;

                    var pd = new createProgressDiv(obj, s);
                    pd.filename.html(fileList);
                    ajaxFormSubmit(form, s, pd, fileArray, obj, null);
                }


            });

            if (s.nestedForms) {
                form.css({
                    'margin': 0,
                    'padding': 0
                });
                uploadLabel.css({
                    position: 'relative',
                    overflow: 'hidden',
                    cursor: 'default'
                });
                fileInput.css({
                    position: 'absolute',
                    'cursor': 'pointer',
                    'top': '0px',
                    'width': '100%',
                    'height': '100%',
                    'left': '0px',
                    'z-index': '100',
                    'opacity': '0.0',
                    'filter': 'alpha(opacity=0)',
                    '-ms-filter': "alpha(opacity=0)",
                    '-khtml-opacity': '0.0',
                    '-moz-opacity': '0.0'
                });
                form.appendTo(uploadLabel);

            } else {
                form.appendTo($('body'));
                form.css({
                    margin: 0,
                    padding: 0,
                    display: 'block',
                    position: 'absolute',
                    left: '-250px'
                });
                if (navigator.appVersion.indexOf("MSIE ") != -1) //IE Browser
                {
                    uploadLabel.attr('for', fileUploadId);
                } else {
                    uploadLabel.click(function () {
                        fileInput.click();
                    });
                }
            }
        }


        function defaultProgressBar(obj, s) {

            this.statusbar = $("<div class='ajax-file-upload-statusbar'></div>");
            this.preview = $("<img class='ajax-file-upload-preview' />").width(s.previewWidth).height(s.previewHeight).appendTo(this.statusbar);
            this.filename = $("<div class='ajax-file-upload-filename'></div>").appendTo(this.statusbar);
            this.progressDiv = $("<div class='ajax-file-upload-progress'>").appendTo(this.statusbar).hide();
            this.progressbar = $("<div class='ajax-file-upload-bar'></div>").appendTo(this.progressDiv);
            this.abort = $("<div>" + s.abortStr + "</div>").appendTo(this.statusbar).hide();
            this.cancel = $("<div>" + s.cancelStr + "</div>").appendTo(this.statusbar).hide();
            this.done = $("<div>" + s.doneStr + "</div>").appendTo(this.statusbar).hide();
            this.download = $("<div>" + s.downloadStr + "</div>").appendTo(this.statusbar).hide();
            this.del = $("<div>" + s.deletelStr + "</div>").appendTo(this.statusbar).hide();

            this.abort.addClass("ajax-file-upload-red");
            this.done.addClass("ajax-file-upload-green");
            this.download.addClass("ajax-file-upload-green");
            this.cancel.addClass("ajax-file-upload-red");
            this.del.addClass("ajax-file-upload-red");

            return this;
        }

        function createProgressDiv(obj, s) {
            var bar = null;
            if (s.customProgressBar)
                bar = new s.customProgressBar(obj, s);
            else
                bar = new defaultProgressBar(obj, s);

            bar.abort.addClass(obj.formGroup);
            bar.abort.addClass(s.abortButtonClass);

            bar.cancel.addClass(obj.formGroup);
            bar.cancel.addClass(s.cancelButtonClass);

            if (s.extraHTML)
                bar.extraHTML = $("<div class='extrahtml'>" + s.extraHTML() + "</div>").insertAfter(bar.filename);

            if (s.uploadQueueOrder == 'bottom')
                $(obj.container).append(bar.statusbar);
            else
                $(obj.container).prepend(bar.statusbar);
            return bar;
        }


        function ajaxFormSubmit(form, s, pd, fileArray, obj, file) {
            var currentXHR = null;
            var options = {
                cache: false,
                contentType: false,
                processData: false,
                forceSync: false,
                type: s.method,
                data: s.formData,
                formData: s.fileData,
                dataType: s.returnType,
                beforeSubmit: function (formData, $form, options) {
                    if (s.onSubmit.call(this, fileArray) != false) {
                        if (s.dynamicFormData) {
                            var sData = serializeData(s.dynamicFormData());
                            if (sData) {
                                for (var j = 0; j < sData.length; j++) {
                                    if (sData[j]) {
                                        if (s.fileData != undefined) options.formData.append(sData[j][0], sData[j][1]);
                                        else options.data[sData[j][0]] = sData[j][1];
                                    }
                                }
                            }
                        }

                        if (s.extraHTML) {
                            $(pd.extraHTML).find("input,select,textarea").each(function (i, items) {
                                if (s.fileData != undefined) options.formData.append($(this).attr('name'), $(this).val());
                                else options.data[$(this).attr('name')] = $(this).val();
                            });
                        }
                        return true;
                    }
                    pd.statusbar.append("<div class='" + s.errorClass + "'>" + s.uploadErrorStr + "</div>");
                    pd.cancel.show()
                    form.remove();
                    pd.cancel.click(function () {
                        mainQ.splice(mainQ.indexOf(form), 1);
                        removeExistingFileName(obj, fileArray);
                        pd.statusbar.remove();
                        s.onCancel.call(obj, fileArray, pd);
                        obj.selectedFiles -= fileArray.length; //reduce selected File count
                        updateFileCounter(s, obj);
                    });
                    return false;
                },
                beforeSend: function (xhr, o) {
                    if (jQuery('.uploader_queue').is(':visible')) {
                    } else {
                        Buckty.uploaderQueue();
                    }
                    ;
                    pd.progressDiv.show();
                    $('.uploading_queue .drag').remove();
                    pd.cancel.hide();
                    pd.done.hide();
                    if (s.showAbort) {
                        pd.abort.show();
                        pd.abort.click(function () {
                            removeExistingFileName(obj, fileArray);
                            xhr.abort();
                            obj.selectedFiles -= fileArray.length; //reduce selected File count
                            s.onAbort.call(obj, fileArray, pd);

                        });
                    }
                    if (!feature.formdata) //For iframe based push
                    {
                        pd.progressbar.width('5%');
                    } else pd.progressbar.width('1%'); //Fix for small files
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    //Fix for smaller file uploads in MAC
                    if (percentComplete > 98) percentComplete = 98;

                    var percentVal = percentComplete + '%';
                    if (percentComplete > 1) pd.progressbar.width(percentVal)
                    if (s.showProgress) {
                        pd.progressbar.html(percentVal);
                        pd.progressbar.css('text-align', 'center');
                    }

                },
                success: function (data, message, xhr) {
                    pd.cancel.remove();
                    progressQ.pop();
                    //For custom errors.
                    if (s.returnType == "json" && $.type(data) == "object" && data.hasOwnProperty(s.customErrorKeyStr)) {
                        pd.abort.hide();
                        var msg = data[s.customErrorKeyStr];
                        s.onError.call(this, fileArray, 200, msg, pd);
                        if (s.showStatusAfterError) {
                            pd.progressDiv.hide();
                            pd.statusbar.append("<span class='" + s.errorClass + "'>Rejected</span>");
                        } else {
                            pd.statusbar.hide();
                            pd.statusbar.remove();
                        }
                        pd.progressDiv.hide();
                        obj.selectedFiles -= fileArray.length; //reduce selected File count
                        form.remove();
                        return;
                    }
                    obj.responses.push(data);
                    pd.progressbar.width('100%')
                    if (s.showProgress) {
                        pd.progressbar.html('Uploaded');
                        pd.progressbar.show();
                    }

                    pd.abort.hide();
                    s.onSuccess.call(this, fileArray, data, xhr, pd);
                    if (s.showStatusAfterSuccess) {
                        if (s.showDone) {
                            pd.done.show();
                            pd.done.click(function () {
                                pd.statusbar.hide("slow");
                                pd.statusbar.remove();
                            });
                        } else {
                            pd.done.hide();
                        }
                        if (s.showDelete) {
                            pd.del.show();
                            pd.del.click(function () {
                                removeExistingFileName(obj, fileArray);
                                pd.statusbar.hide().remove();
                                if (s.deleteCallback) s.deleteCallback.call(this, data, pd);
                                obj.selectedFiles -= fileArray.length; //reduce selected File count
                                updateFileCounter(s, obj);

                            });
                        } else {
                            pd.del.hide();
                        }
                    } else {
                        pd.statusbar.hide("slow");
                        pd.statusbar.remove();

                    }
                    if (s.showDownload) {
                        pd.download.show();
                        pd.download.click(function () {
                            if (s.downloadCallback) s.downloadCallback(data);
                        });
                    }
                    form.remove();
                },
                error: function (xhr, status, errMsg) {
                    pd.cancel.remove();
                    progressQ.pop();
                    pd.abort.hide();
                    if (xhr.statusText == "abort") //we aborted it
                    {
                        pd.statusbar.hide("slow").remove();
                        updateFileCounter(s, obj);

                    } else {
                        s.onError.call(this, fileArray, status, errMsg, pd);
                        if (s.showStatusAfterError) {
                            pd.progressDiv.hide();
                            pd.statusbar.append("<span class='" + s.errorClass + "'>ERROR: " + errMsg + "</span>");
                        } else {
                            pd.statusbar.hide();
                            pd.statusbar.remove();
                        }
                        obj.selectedFiles -= fileArray.length; //reduce selected File count
                    }

                    form.remove();
                }
            };

            if (s.showPreview && file != null) {
                if (file.type.toLowerCase().split("/").shift() == "image") getSrcToPreview(file, pd.preview);
            }

            if (s.autoSubmit) {
                form.ajaxForm(options);
                mainQ.push(form);
                submitPendingUploads();

            } else {
                if (s.showCancel) {
                    pd.cancel.show();
                    pd.cancel.click(function () {
                        mainQ.splice(mainQ.indexOf(form), 1);
                        removeExistingFileName(obj, fileArray);
                        form.remove();
                        pd.statusbar.remove();
                        s.onCancel.call(obj, fileArray, pd);
                        obj.selectedFiles -= fileArray.length; //reduce selected File count
                        updateFileCounter(s, obj);
                    });
                }
                form.ajaxForm(options);
            }

        }

        return this;

    }
}(jQuery));

$.fn.multiSelect = function (o) {
    var defaults = {
        multiselect: true,
        selected: 'selected',
        filter: ' > *',
        unselectOn: true,
        keepSelection: true,
        list: $(this).selector,
        e: null,
        element: null,
        start: false,
        stop: false,
        unselecting: true
    }
    return this.each(function (k, v) {
        var options = $.extend({}, defaults, o || {});
        // selector - parent, assign listener to children only
        $(document).on('mousedown', options.list + options.filter, function (e) {
            if (e.which == 1) {
                if (options.handle != undefined && !$(e.target).is(options.handle)) {
                    return true;
                }
                options.e = e;
                options.element = $(this);
                multiSelect(options);
            }
            return true;
        });

        if (options.unselectOn) {
            // event to unselect

            $('.files_container,.vert_block').on('mousedown', options.unselectOn, function (e) {
                if (!$(e.target).parents().is(options.list) && e.which != 3) {
                    $(options.list + ' .' + options.selected).removeClass(options.selected);
                    if (options.unselecting != false) {
                        options.unselecting();
                    }
                }
            });
        }

    });


}

function multiSelect(o) {

    var target = o.e.target;
    var element = o.element;
    var list = o.list;

    if ($(element).hasClass('ui-sortable-helper')) {
        return false;
    }

    if (o.start != false) {
        var start = o.start(o.e, $(element));
        if (start == false) {
            return false;
        }
    }

    if (o.e.shiftKey && o.multiselect) {
        // get one already selected row
        $(element).addClass(o.selected);
        first = $(o.list).find('.' + o.selected).first().index();
        last = $(o.list).find('.' + o.selected).last().index();

        // if we hold shift and try to select last element that is upper in the list
        if (last < first) {
            firstHolder = first;
            first = last;
            last = firstHolder;
        }

        if (first == -1 || last == -1) {
            return false;
        }

        $(o.list).find('.' + o.selected).removeClass(o.selected);

        var num = last - first;
        var x = first;

        for (i = 0; i <= num; i++) {
            $(list).find(o.filter).eq(x).addClass(o.selected);
            x++;
        }
    } else if ((o.e.ctrlKey || o.e.metaKey) && o.multiselect) {
        // reset selection
        if ($(element).hasClass(o.selected)) {
            $(element).removeClass(o.selected);
        } else {
            $(element).addClass(o.selected);
        }
    }

    if (o.stop != false) {
        o.stop($(list).find('.' + o.selected), $(element));
    }

}
(function ($) {

    $.widget('ui.tagit', {
        options: {
            allowDuplicates: false,
            caseSensitive: true,
            fieldName: 'tags',
            placeholderText: null,   // Sets `placeholder` attr on input field.
            readOnly: false,  // Disables editing.
            removeConfirmation: false,  // Require confirmation to remove tags.
            tagLimit: null,   // Max number of tags allowed (null for unlimited).

            // Used for autocomplete, unless you override `autocomplete.source`.
            availableTags: [],

            // Use to override or add any options to the autocomplete widget.
            //
            // By default, autocomplete.source will map to availableTags,
            // unless overridden.
            autocomplete: {},

            // Shows autocomplete before the user even types anything.
            showAutocompleteOnFocus: false,

            // When enabled, quotes are unneccesary for inputting multi-word tags.
            allowSpaces: false,

            // The below options are for using a single field instead of several
            // for our form values.
            //
            // When enabled, will use a single hidden field for the form,
            // rather than one per tag. It will delimit tags in the field
            // with singleFieldDelimiter.
            //
            // The easiest way to use singleField is to just instantiate tag-it
            // on an INPUT element, in which case singleField is automatically
            // set to true, and singleFieldNode is set to that element. This
            // way, you don't need to fiddle with these options.
            singleField: false,

            // This is just used when preloading data from the field, and for
            // populating the field with delimited tags as the user adds them.
            singleFieldDelimiter: ',',

            // Set this to an input DOM node to use an existing form field.
            // Any text in it will be erased on init. But it will be
            // populated with the text of tags as they are created,
            // delimited by singleFieldDelimiter.
            //
            // If this is not set, we create an input node for it,
            // with the name given in settings.fieldName.
            singleFieldNode: null,

            // Whether to animate tag removals or not.
            animate: true,

            // Optionally set a tabindex attribute on the input that gets
            // created for tag-it.
            tabIndex: null,

            // Event callbacks.
            beforeTagAdded: null,
            afterTagAdded: null,

            beforeTagRemoved: null,
            afterTagRemoved: null,

            onTagClicked: null,
            onTagLimitExceeded: null,


            // DEPRECATED:
            //
            // /!\ These event callbacks are deprecated and WILL BE REMOVED at some
            // point in the future. They're here for backwards-compatibility.
            // Use the above before/after event callbacks instead.
            onTagAdded: null,
            onTagRemoved: null,
            // `autocomplete.source` is the replacement for tagSource.
            tagSource: null
            // Do not use the above deprecated options.
        },

        _create: function () {
            // for handling static scoping inside callbacks
            var that = this;

            // There are 2 kinds of DOM nodes this widget can be instantiated on:
            //     1. UL, OL, or some element containing either of these.
            //     2. INPUT, in which case 'singleField' is overridden to true,
            //        a UL is created and the INPUT is hidden.
            if (this.element.is('input')) {
                this.tagList = $('<ul></ul>').insertAfter(this.element);
                this.options.singleField = true;
                this.options.singleFieldNode = this.element;
                this.element.addClass('tagit-hidden-field');
            } else {
                this.tagList = this.element.find('ul, ol').andSelf().last();
            }

            this.tagInput = $('<input type="text" />').addClass('ui-widget-content');

            if (this.options.readOnly) this.tagInput.attr('disabled', 'disabled');

            if (this.options.tabIndex) {
                this.tagInput.attr('tabindex', this.options.tabIndex);
            }

            if (this.options.placeholderText) {
                this.tagInput.attr('placeholder', this.options.placeholderText);
            }

            if (!this.options.autocomplete.source) {
                this.options.autocomplete.source = function (search, showChoices) {
                    var filter = search.term.toLowerCase();
                    var choices = $.grep(this.options.availableTags, function (element) {
                        // Only match autocomplete options that begin with the search term.
                        // (Case insensitive.)
                        return (element.toLowerCase().indexOf(filter) === 0);
                    });
                    if (!this.options.allowDuplicates) {
                        choices = this._subtractArray(choices, this.assignedTags());
                    }
                    showChoices(choices);
                };
            }

            if (this.options.showAutocompleteOnFocus) {
                this.tagInput.focus(function (event, ui) {
                    that._showAutocomplete();
                });

                if (typeof this.options.autocomplete.minLength === 'undefined') {
                    this.options.autocomplete.minLength = 0;
                }
            }

            // Bind autocomplete.source callback functions to this context.
            if ($.isFunction(this.options.autocomplete.source)) {
                this.options.autocomplete.source = $.proxy(this.options.autocomplete.source, this);
            }

            // DEPRECATED.
            if ($.isFunction(this.options.tagSource)) {
                this.options.tagSource = $.proxy(this.options.tagSource, this);
            }

            this.tagList
                .addClass('tagit')
                .addClass('ui-widget ui-widget-content ui-corner-all')
                // Create the input field.
                .append($('<li class="tagit-new"></li>').append(this.tagInput))
                .click(function (e) {
                    var target = $(e.target);
                    if (target.hasClass('tagit-label')) {
                        var tag = target.closest('.tagit-choice');
                        if (!tag.hasClass('removed')) {
                            that._trigger('onTagClicked', e, {tag: tag, tagLabel: that.tagLabel(tag)});
                        }
                    } else {
                        // Sets the focus() to the input field, if the user
                        // clicks anywhere inside the UL. This is needed
                        // because the input field needs to be of a small size.
                        that.tagInput.focus();
                    }
                });

            // Single field support.
            var addedExistingFromSingleFieldNode = false;
            if (this.options.singleField) {
                if (this.options.singleFieldNode) {
                    // Add existing tags from the input field.
                    var node = $(this.options.singleFieldNode);
                    var tags = node.val().split(this.options.singleFieldDelimiter);
                    node.val('');
                    $.each(tags, function (index, tag) {
                        that.createTag(tag, null, true);
                        addedExistingFromSingleFieldNode = true;
                    });
                } else {
                    // Create our single field input after our list.
                    this.options.singleFieldNode = $('<input type="hidden" style="display:none;" value="" name="' + this.options.fieldName + '" />');
                    this.tagList.after(this.options.singleFieldNode);
                }
            }

            // Add existing tags from the list, if any.
            if (!addedExistingFromSingleFieldNode) {
                this.tagList.children('li').each(function () {
                    if (!$(this).hasClass('tagit-new')) {
                        that.createTag($(this).text(), $(this).attr('class'), true);
                        $(this).remove();
                    }
                });
            }

            // Events.
            this.tagInput
                .keydown(function (event) {
                    // Backspace is not detected within a keypress, so it must use keydown.
                    if (event.which == $.ui.keyCode.BACKSPACE && that.tagInput.val() === '') {
                        var tag = that._lastTag();
                        if (!that.options.removeConfirmation || tag.hasClass('remove')) {
                            // When backspace is pressed, the last tag is deleted.
                            that.removeTag(tag);
                        } else if (that.options.removeConfirmation) {
                            tag.addClass('remove ui-state-highlight');
                        }
                    } else if (that.options.removeConfirmation) {
                        that._lastTag().removeClass('remove ui-state-highlight');
                    }

                    // Comma/Space/Enter are all valid delimiters for new tags,
                    // except when there is an open quote or if setting allowSpaces = true.
                    // Tab will also create a tag, unless the tag input is empty,
                    // in which case it isn't caught.
                    if (
                        (event.which === $.ui.keyCode.COMMA && event.shiftKey === false) ||
                        event.which === $.ui.keyCode.ENTER ||
                        (
                            event.which == $.ui.keyCode.TAB &&
                            that.tagInput.val() !== ''
                        ) ||
                        (
                            event.which == $.ui.keyCode.SPACE &&
                            that.options.allowSpaces !== true &&
                            (
                                $.trim(that.tagInput.val()).replace(/^s*/, '').charAt(0) != '"' ||
                                (
                                    $.trim(that.tagInput.val()).charAt(0) == '"' &&
                                    $.trim(that.tagInput.val()).charAt($.trim(that.tagInput.val()).length - 1) == '"' &&
                                    $.trim(that.tagInput.val()).length - 1 !== 0
                                )
                            )
                        )
                    ) {
                        // Enter submits the form if there's no text in the input.
                        if (!(event.which === $.ui.keyCode.ENTER && that.tagInput.val() === '')) {
                            event.preventDefault();
                        }

                        // Autocomplete will create its own tag from a selection and close automatically.
                        if (!(that.options.autocomplete.autoFocus && that.tagInput.data('autocomplete-open'))) {
                            that.tagInput.autocomplete('close');
                            that.createTag(that._cleanedInput());
                        }
                    }
                }).blur(function (e) {
                // Create a tag when the element loses focus.
                // If autocomplete is enabled and suggestion was clicked, don't add it.
                if (!that.tagInput.data('autocomplete-open')) {
                    that.createTag(that._cleanedInput());
                }
            });

            // Autocomplete.
            if (this.options.availableTags || this.options.tagSource || this.options.autocomplete.source) {
                var autocompleteOptions = {
                    select: function (event, ui) {
                        that.createTag(ui.item.value);
                        // Preventing the tag input to be updated with the chosen value.
                        return false;
                    }
                };
                $.extend(autocompleteOptions, this.options.autocomplete);

                // tagSource is deprecated, but takes precedence here since autocomplete.source is set by default,
                // while tagSource is left null by default.
                autocompleteOptions.source = this.options.tagSource || autocompleteOptions.source;

                this.tagInput.autocomplete(autocompleteOptions).bind('autocompleteopen.tagit', function (event, ui) {
                    that.tagInput.data('autocomplete-open', true);
                }).bind('autocompleteclose.tagit', function (event, ui) {
                    that.tagInput.data('autocomplete-open', false);
                });

                this.tagInput.autocomplete('widget').addClass('tagit-autocomplete');
            }
        },

        destroy: function () {
            $.Widget.prototype.destroy.call(this);

            this.element.unbind('.tagit');
            this.tagList.unbind('.tagit');

            this.tagInput.removeData('autocomplete-open');

            this.tagList.removeClass([
                'tagit',
                'ui-widget',
                'ui-widget-content',
                'ui-corner-all',
                'tagit-hidden-field'
            ].join(' '));

            if (this.element.is('input')) {
                this.element.removeClass('tagit-hidden-field');
                this.tagList.remove();
            } else {
                this.element.children('li').each(function () {
                    if ($(this).hasClass('tagit-new')) {
                        $(this).remove();
                    } else {
                        $(this).removeClass([
                            'tagit-choice',
                            'ui-widget-content',
                            'ui-state-default',
                            'ui-state-highlight',
                            'ui-corner-all',
                            'remove',
                            'tagit-choice-editable',
                            'tagit-choice-read-only'
                        ].join(' '));

                        $(this).text($(this).children('.tagit-label').text());
                    }
                });

                if (this.singleFieldNode) {
                    this.singleFieldNode.remove();
                }
            }

            return this;
        },

        _cleanedInput: function () {
            // Returns the contents of the tag input, cleaned and ready to be passed to createTag
            return $.trim(this.tagInput.val().replace(/^"(.*)"$/, '$1'));
        },

        _lastTag: function () {
            return this.tagList.find('.tagit-choice:last:not(.removed)');
        },

        _tags: function () {
            return this.tagList.find('.tagit-choice:not(.removed)');
        },

        assignedTags: function () {
            // Returns an array of tag string values
            var that = this;
            var tags = [];
            if (this.options.singleField) {
                tags = $(this.options.singleFieldNode).val().split(this.options.singleFieldDelimiter);
                if (tags[0] === '') {
                    tags = [];
                }
            } else {
                this._tags().each(function () {
                    tags.push(that.tagLabel(this));
                });
            }
            return tags;
        },

        _updateSingleTagsField: function (tags) {
            // Takes a list of tag string values, updates this.options.singleFieldNode.val to the tags delimited by this.options.singleFieldDelimiter
            $(this.options.singleFieldNode).val(tags.join(this.options.singleFieldDelimiter)).trigger('change');
        },

        _subtractArray: function (a1, a2) {
            var result = [];
            for (var i = 0; i < a1.length; i++) {
                if ($.inArray(a1[i], a2) == -1) {
                    result.push(a1[i]);
                }
            }
            return result;
        },

        tagLabel: function (tag) {
            // Returns the tag's string label.
            if (this.options.singleField) {
                return $(tag).find('.tagit-label:first').text();
            } else {
                return $(tag).find('input:first').val();
            }
        },

        _showAutocomplete: function () {
            this.tagInput.autocomplete('search', '');
        },

        _findTagByLabel: function (name) {
            var that = this;
            var tag = null;
            this._tags().each(function (i) {
                if (that._formatStr(name) == that._formatStr(that.tagLabel(this))) {
                    tag = $(this);
                    return false;
                }
            });
            return tag;
        },

        _isNew: function (name) {
            return !this._findTagByLabel(name);
        },

        _formatStr: function (str) {
            if (this.options.caseSensitive) {
                return str;
            }
            return $.trim(str.toLowerCase());
        },

        _effectExists: function (name) {
            return Boolean($.effects && ($.effects[name] || ($.effects.effect && $.effects.effect[name])));
        },

        createTag: function (value, additionalClass, duringInitialization) {
            var that = this;

            value = $.trim(value);

            if (this.options.preprocessTag) {
                value = this.options.preprocessTag(value);
            }

            if (value === '') {
                return false;
            }

            if (!this.options.allowDuplicates && !this._isNew(value)) {
                var existingTag = this._findTagByLabel(value);
                if (this._trigger('onTagExists', null, {
                        existingTag: existingTag,
                        duringInitialization: duringInitialization
                    }) !== false) {
                    if (this._effectExists('highlight')) {
                        existingTag.effect('highlight');
                    }
                }
                return false;
            }

            if (this.options.tagLimit && this._tags().length >= this.options.tagLimit) {
                this._trigger('onTagLimitExceeded', null, {duringInitialization: duringInitialization});
                return false;
            }

            var label = $(this.options.onTagClicked ? '<a class="tagit-label"></a>' : '<span class="tagit-label"></span>').text(value);

            // Create tag.
            var tag = $('<li></li>')
                .addClass('tagit-choice ui-widget-content ui-state-default ui-corner-all')
                .addClass(additionalClass)
                .append(label);

            if (this.options.readOnly) {
                tag.addClass('tagit-choice-read-only');
            } else {
                tag.addClass('tagit-choice-editable');
                // Button for removing the tag.
                var removeTagIcon = $('<span></span>')
                    .addClass('ui-icon ui-icon-close');
                var removeTag = $('<a><span class="text-icon">\xd7</span></a>') // \xd7 is an X
                    .addClass('tagit-close')
                    .append(removeTagIcon)
                    .click(function (e) {
                        // Removes a tag when the little 'x' is clicked.
                        that.removeTag(tag);
                    });
                tag.append(removeTag);
            }

            // Unless options.singleField is set, each tag has a hidden input field inline.
            if (!this.options.singleField) {
                var escapedValue = label.html();
                tag.append('<input type="hidden" value="' + escapedValue + '" name="' + this.options.fieldName + '" class="tagit-hidden-field" />');
            }

            if (this._trigger('beforeTagAdded', null, {
                    tag: tag,
                    tagLabel: this.tagLabel(tag),
                    duringInitialization: duringInitialization
                }) === false) {
                return;
            }

            if (this.options.singleField) {
                var tags = this.assignedTags();
                tags.push(value);
                this._updateSingleTagsField(tags);
            }

            // DEPRECATED.
            this._trigger('onTagAdded', null, tag);

            this.tagInput.val('');

            // Insert tag.
            this.tagInput.parent().before(tag);

            this._trigger('afterTagAdded', null, {
                tag: tag,
                tagLabel: this.tagLabel(tag),
                duringInitialization: duringInitialization
            });

            if (this.options.showAutocompleteOnFocus && !duringInitialization) {
                setTimeout(function () {
                    that._showAutocomplete();
                }, 0);
            }
        },

        removeTag: function (tag, animate) {
            animate = typeof animate === 'undefined' ? this.options.animate : animate;

            tag = $(tag);

            // DEPRECATED.
            this._trigger('onTagRemoved', null, tag);

            if (this._trigger('beforeTagRemoved', null, {tag: tag, tagLabel: this.tagLabel(tag)}) === false) {
                return;
            }

            if (this.options.singleField) {
                var tags = this.assignedTags();
                var removedTagLabel = this.tagLabel(tag);
                tags = $.grep(tags, function (el) {
                    return el != removedTagLabel;
                });
                this._updateSingleTagsField(tags);
            }

            if (animate) {
                tag.addClass('removed'); // Excludes this tag from _tags.
                var hide_args = this._effectExists('blind') ? ['blind', {direction: 'horizontal'}, 'fast'] : ['fast'];

                var thisTag = this;
                hide_args.push(function () {
                    tag.remove();
                    thisTag._trigger('afterTagRemoved', null, {tag: tag, tagLabel: thisTag.tagLabel(tag)});
                });

                tag.fadeOut('fast').hide.apply(tag, hide_args).dequeue();
            } else {
                tag.remove();
                this._trigger('afterTagRemoved', null, {tag: tag, tagLabel: this.tagLabel(tag)});
            }

        },

        removeTagByLabel: function (tagLabel, animate) {
            var toRemove = this._findTagByLabel(tagLabel);
            if (!toRemove) {
                throw "No such tag exists with the name '" + tagLabel + "'";
            }
            this.removeTag(toRemove, animate);
        },

        removeAll: function () {
            // Removes all tags.
            var that = this;
            this._tags().each(function (index, tag) {
                that.removeTag(tag, false);
            });
        }

    });
})(jQuery);
(function ($) {
    function isDOMAttrModifiedSupported() {
        var p = document.createElement('p');
        var flag = false;

        if (p.addEventListener) {
            p.addEventListener('DOMAttrModified', function () {
                flag = true
            }, false);
        } else if (p.attachEvent) {
            p.attachEvent('onDOMAttrModified', function () {
                flag = true
            });
        } else {
            return false;
        }
        p.setAttribute('id', 'target');
        return flag;
    }

    function checkAttributes(chkAttr, e) {
        if (chkAttr) {
            var attributes = this.data('attr-old-value');

            if (e.attributeName.indexOf('style') >= 0) {
                if (!attributes['style'])
                    attributes['style'] = {}; //initialize
                var keys = e.attributeName.split('.');
                e.attributeName = keys[0];
                e.oldValue = attributes['style'][keys[1]]; //old value
                e.newValue = keys[1] + ':'
                    + this.prop("style")[$.camelCase(keys[1])]; //new value
                attributes['style'][keys[1]] = e.newValue;
            } else {
                e.oldValue = attributes[e.attributeName];
                e.newValue = this.attr(e.attributeName);
                attributes[e.attributeName] = e.newValue;
            }

            this.data('attr-old-value', attributes); //update the old value object
        }
    }

    //initialize Mutation Observer
    var MutationObserver = window.MutationObserver
        || window.WebKitMutationObserver;

    $.fn.attrchange = function (a, b) {
        if (typeof a == 'object') {//core
            var cfg = {
                trackValues: false,
                callback: $.noop
            };
            //backward compatibility
            if (typeof a === "function") {
                cfg.callback = a;
            } else {
                $.extend(cfg, a);
            }

            if (cfg.trackValues) { //get attributes old value
                this.each(function (i, el) {
                    var attributes = {};
                    for (var attr, i = 0, attrs = el.attributes, l = attrs.length; i < l; i++) {
                        attr = attrs.item(i);
                        attributes[attr.nodeName] = attr.value;
                    }
                    $(this).data('attr-old-value', attributes);
                });
            }

            if (MutationObserver) { //Modern Browsers supporting MutationObserver
                var mOptions = {
                    subtree: false,
                    attributes: true,
                    attributeOldValue: cfg.trackValues
                };
                var observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (e) {
                        var _this = e.target;
                        //get new value if trackValues is true
                        if (cfg.trackValues) {
                            e.newValue = $(_this).attr(e.attributeName);
                        }
                        if ($(_this).data('attrchange-status') === 'connected') { //execute if connected
                            cfg.callback.call(_this, e);
                        }
                    });
                });

                return this.data('attrchange-method', 'Mutation Observer').data('attrchange-status', 'connected')
                    .data('attrchange-obs', observer).each(function () {
                        observer.observe(this, mOptions);
                    });
            } else if (isDOMAttrModifiedSupported()) { //Opera
                //Good old Mutation Events
                return this.data('attrchange-method', 'DOMAttrModified').data('attrchange-status', 'connected').on('DOMAttrModified', function (event) {
                    if (event.originalEvent) {
                        event = event.originalEvent;
                    }//jQuery normalization is not required
                    event.attributeName = event.attrName; //property names to be consistent with MutationObserver
                    event.oldValue = event.prevValue; //property names to be consistent with MutationObserver
                    if ($(this).data('attrchange-status') === 'connected') { //disconnected logically
                        cfg.callback.call(this, event);
                    }
                });
            } else if ('onpropertychange' in document.body) { //works only in IE
                return this.data('attrchange-method', 'propertychange').data('attrchange-status', 'connected').on('propertychange', function (e) {
                    e.attributeName = window.event.propertyName;
                    //to set the attr old value
                    checkAttributes.call($(this), cfg.trackValues, e);
                    if ($(this).data('attrchange-status') === 'connected') { //disconnected logically
                        cfg.callback.call(this, e);
                    }
                });
            }
            return this;
        } else if (typeof a == 'string' && $.fn.attrchange.hasOwnProperty('extensions') &&
            $.fn.attrchange['extensions'].hasOwnProperty(a)) { //extensions/options
            return $.fn.attrchange['extensions'][a].call(this, b);
        }
    }
})(jQuery);
(function ($) {
    $.oauthpopup = function (options) {
        if (!options || !options.path) {
            throw new Error("options.path must not be empty");
        }
        options = $.extend({
            windowName: 'ConnectWithOAuth' // should not include space for IE
            ,
            windowOptions: 'location=0,status=0,width=900,height=500',
            callback: function () {
                window.location.reload();
            }
        }, options);

        var oauthWindow = window.open(options.path, options.windowName, options.windowOptions);
        var oauthInterval = window.setInterval(function () {
            if (oauthWindow.closed) {
                window.clearInterval(oauthInterval);
                options.callback();
            }
        }, 1000);
    };

    //bind to element and pop oauth when clicked
    $.fn.oauthpopup = function (options) {
        $this = $(this);
        $this.click($.oauthpopup.bind(this, options));
    };
})(jQuery);

(function ($) {

    $.fn.niceSelect = function () {

        // Create custom markup
        this.each(function () {
            var select = $(this);

            if (!select.next().hasClass('nice-select')) {
                select.after('<div class="nice-select ' + (select.attr('class') || '') + (select.attr('disabled') ? 'disabled' : '" tabindex="0') +
                    '"><span class="current"></span><ul class="list"></ul></div>');

                var dropdown = select.next();
                var options = select.find('option');
                var selected = select.find('option:selected');

                dropdown.find('.current').html(selected.data('display') || selected.text());

                options.each(function () {
                    var display = $(this).data('display');
                    dropdown.find('ul').append('<li class="option ' + ($(this).is(':selected') ? 'selected' : '') +
                        '" data-value="' + $(this).val() + (display ? '" data-display="' + display : '') + '">' +
                        $(this).text() + '</li>');
                });
            }
        });

        /* Event listeners */

        // Unbind existing events in case that the plugin has been initialized before
        $(document).off('.nice_select');

        // Open/close
        $(document).on('click.nice_select', '.nice-select', function (event) {
            var dropdown = $(this);

            $('.nice-select').not(dropdown).removeClass('open');
            dropdown.toggleClass('open');

            if (dropdown.hasClass('open')) {
                dropdown.find('.option');
                dropdown.find('.focus').removeClass('focus');
                dropdown.find('.selected').addClass('focus');
            } else {
                dropdown.focus();
            }
        });

        // Close when clicking outside
        $(document).on('click.nice_select', function (event) {
            if ($(event.target).closest('.nice-select').length === 0) {
                $('.nice-select').removeClass('open').find('.option');
            }
        });

        // Option click
        $(document).on('click.nice_select', '.nice-select .option', function (event) {
            var option = $(this);
            var dropdown = option.closest('.nice-select');

            dropdown.find('.selected').removeClass('selected');
            option.addClass('selected');

            var text = option.data('display') || option.text();
            dropdown.find('.current').text(text);

            dropdown.prev('select').val(option.data('value')).trigger('change');
        });

        // Keyboard events
        $(document).on('keydown.nice_select', '.nice-select', function (event) {
            var dropdown = $(this);
            var focused_option = $(dropdown.find('.focus') || dropdown.find('.list .option.selected'));

            // Space or Enter
            if (event.keyCode == 32 || event.keyCode == 13) {
                if (dropdown.hasClass('open')) {
                    focused_option.trigger('click');
                } else {
                    dropdown.trigger('click');
                }
                return false;
                // Down
            } else if (event.keyCode == 40) {
                if (!dropdown.hasClass('open')) {
                    dropdown.trigger('click');
                } else {
                    if (focused_option.next().length > 0) {
                        dropdown.find('.focus').removeClass('focus');
                        focused_option.next().addClass('focus');
                    }
                }
                return false;
                // Up
            } else if (event.keyCode == 38) {
                if (!dropdown.hasClass('open')) {
                    dropdown.trigger('click');
                } else {
                    if (focused_option.prev().length > 0) {
                        dropdown.find('.focus').removeClass('focus');
                        focused_option.prev().addClass('focus');
                    }
                }
                return false;
                // Esc
            } else if (event.keyCode == 27) {
                if (dropdown.hasClass('open')) {
                    dropdown.trigger('click');
                }
                // Tab
            } else if (event.keyCode == 9) {
                if (dropdown.hasClass('open')) {
                    return false;
                }
            }
        });

    };

}(jQuery));
$(document).ready(function () {
    $('select').niceSelect();
    $(document.body).on('click', 'a', Buckty.handler);
});
$.getScript(site_url + 'assets/js/player/mediaelement-and-player.js');
/**
 * Declare Buckty function.
 */
function Buckty() {
};

var open_folders = [];

Buckty.handler = function (event, url, manual) {
    if (typeof manual === "undefined") {
        manual = false;
    }
    if (typeof url === "undefined") {
        link = $(this);
        if (link.hasClass('selected')) {
            return false;
        }
        if (link.data('no-ajax') === true)
            return;
        var href = link.attr("href"),
            target = (typeof link.data('target') !== "undefined") ? link.data('target') : '#content',
            append = (typeof link.data('append') !== "undefined") ? link.data('append') : false,
            changeUrl = (typeof link.data('change-url') === "undefined") ? true : link.data('change-url'),
            type = (typeof link.data('type') !== "undefined") ? link.data('type') : 'GET';
        if (!href || href === "#" || href === "javascript:void(0);" || href === "javascript:void(0)")
            return;
    } else {
        target = '#content';
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
        try {
            data = JSON.parse(data);
            if (data.error_code === 1) {
                Buckty.toast('exclamation-triangle', data.message);
            }
        } catch (e) {
            var content = $(data).filter(target).html();
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
            Buckty.load_script();
            Buckty.CurrentPage();
        }
    });
    return false;
}

jQuery(function () {
    Buckty.load_script();
    Buckty.uploader();
});

function toast(c, t) {
    var num = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
    ;
    var container = jQuery('#toast-container');
    switch (t) {
        case 'error':
            var bg = 'rgba(182, 0, 0, 0.8)';
            break;
        case 'success':
            var bg = 'rgba(0, 182, 48, 0.8)';
            break;
        default:
            var bg = 'rgba(0, 0, 0, 0.8)';
    }
    container.append('<div id="notification-' + num + '" style="background:' + bg + ' !important;">' + c + '</div>');
    setTimeout(function () {
        jQuery('#notification-' + num).fadeOut('slow').remove();
    }, 5000);
}
jQuery(function () {
    $(document.body).on('click', '.sub_ico', Buckty.TreeView);
});
var selected_items_count = 0;
var dropSettings = {
    accept: '.files_ .file_item',
    hoverClass: "ui-state-active",
    tolerance: "pointer",
    drop: drop
};
var dragSettings = {
    revert: "invalid",
    appendTo: "body",
    cursor: 'move',
    cursorAt: {
        left: 0,
        top: 0
    },
    start: function (event, ui) {
        var count = $('.files_ .ui-selected').length;
        if (count > 1) {
            $(this).addClass('ui-selected');
        } else {
            $('.files_ .ui-selected').removeClass('ui-selected');
            $(this).addClass('ui-selected');
        }
    },
    revert : function(event, ui) {
        $(this).data("ui-draggable").originalPosition = {
            top : 0,
            left : 0
        };
        return !event;
    },
    helper: drag_me
};

var UserUsedSpace = '';

var SelectedFiles = [];

Buckty.socialLog = function (provider) {
    switch (provider) {
        case 'facebook':
            $.oauthpopup({
                path: site_url + 'index.php/login/facebook',
                callback: function () {
                    location.reload();
                }
            });
            break;
        case 'google':
            $.oauthpopup({
                path: site_url + 'index.php/login/google',
                callback: function () {
                    location.reload();
                }
            });
            break;
        case 'twitter':
            $.oauthpopup({
                path: site_url + 'index.php/login/twitter',
                callback: function () {
                    location.reload();
                }
            });
            break;
    }
}
Buckty.load_script = function () {
    if (jQuery('.files_').length) {
        var selected_items_ = $([]),
            offset = {
                top: 0,
                left: 0
            };

        if (current_folder !== 'folder') {
            jQuery(".files_container").selectable({
                filter: 'div:not(.ignore),a:not(.ignore),h3:not(.ignore),img:not(.ignore),h3:not(.title)',
                cancel: 'a',
                selecting: Buckty.ItemInSelection
            });
            var drag = false;
            jQuery('.files_ > div').click(function (e) {

                if (drag === false)
                    if (e.metaKey === false && e.shiftKey === false) {
                        // if command key is pressed don't deselect existing elements
                        $(".files_ > div").removeClass("ui-selected");
                        $(this).addClass('ui-selected');
                    }
                //Buckty.ItemInSelection();
            });
            // starting position of the divs
            jQuery('.folder_,.folder_crumb').droppable(dropSettings);
            jQuery(".item_clickable").draggable(dragSettings);
        }
    }
    $(document.body).find('.file_item').attrchange({
        trackValues: true, // set to true so that the event object is updated with old & new values
        callback: function (evnt) {
            if (evnt.attributeName == "class") { // which attribute you want to watch for changes
                if (evnt.newValue.search(/ui-selected/i) == -1) { // "open" is the class name you search for inside "class" attribute
                    $('.multiple_menu').hide();
                } else {
                    $('.multiple_menu').show();
                }
            }
        }
    });
    jQuery(document.body).on('click', '.right_menu,.drop_down,.files_container,.suggest_box,.multiple_menu', function (e) {
        e.stopPropagation(); // This is the preferred method.
        e.preventDefault();

    });
    jQuery('.context,.drop_down,.multiple_menu,.list_item').mousedown(function (e) {
        e.stopPropagation();
    });
    jQuery(document.body).on('click', '.drop_m', Buckty.Dropdown);
    jQuery('.item_clickable,.files_,.ul_folders .folder_').on("contextmenu", Buckty.Context);
    jQuery('body,.files_container').mousedown(Buckty.mouseEvent);
    jQuery(document).click(Buckty.documentEvent);
    jQuery('.files_container').mousedown(Buckty.documentEvent);
}

$(document.body).on('click', '#change_view', function (event) {
    var view = $(this).attr('view');
    jQuery(this).find('i').toggleClass('fa-th fa-list');
    if (view === 'grid') {
        jQuery(this).attr('view', 'list');
    } else {
        jQuery(this).attr('view', 'grid');
    }
    if (jQuery('.files_').hasClass('list')) {
        jQuery('.files_').addClass('grid');
        jQuery('.files_').removeClass('list');
    } else if (jQuery('.files_').hasClass('grid')) {
        jQuery('.files_').addClass('list');
        jQuery('.files_').removeClass('grid');
    }
    Buckty.removeCookie('view');
    Buckty.setCookie('view', view, 1000);
});
$(document).on('submit', '#updateUser', function (e) {
    e.preventDefault();
    var data = $(this).serializeArray();
    data.push({
        name: 'csrf_Buckty',
        value: Buckty.Buckty_getToken
    });
    $.post(site_url + '/user/update', data).done(function (d) {
        var d = JSON.parse(d);
        if (d.error_code === 0) {
            Buckty.toast('info', d.message);
        } else if (d.error_code === 1) {
            Buckty.toast('info', d.message);
        }
    });
    return false;
});
$(document.body).on('dblclick', '.files_ .file_item', function () {
    var item = $(this).data('item');
    var type = $(this).data('item-type');
    if (type === 'file') {
        Buckty.preview(item, type);
    } else if (type === 'folder') {
        Buckty.handler('', site_url + 'folders/' + item);
    }

});
$(document.body).on('click','.files_ .file_item',function(){
    var item = $(this).data('item');
    var type = $(this).data('item-type');
    if ($(window).width() <= 756) {
        if (type === 'folder') {
            Buckty.handler('', site_url + 'folders/' + item);
        }
    }
});

function drag_me(event, ui) {
    var inSel = Buckty.ItemInSelection();
    $.each(inSel,function(){
        var v = this.split('/');
        if(v[1] === 'folder'){
            if($('#Treefolder_'+v[0]).siblings('ul').is(':visible')) {
                $('#Treefolder_'+v[0]).siblings('ul').hide();
                $('#Treefolder_' + v[0]).siblings('i').toggleClass('fa-caret-down fa-caret-right');
            }
        }
    });
    var item_data = event.currentTarget.dataset;
    var counts = $('.files_ .ui-selected').length;
    if (counts === 0) {
        counts = 1;
    }
    jQuery('#' + this.id).addClass('ui-selected');
    if(item_data.itemType === 'folder'){
        if($('#Treefolder_'+item_data.item).siblings('ul').is(':visible')) {
            $('#Treefolder_'+item_data.item).siblings('ul').hide();
            $('#Treefolder_' + item_data.item).siblings('i').toggleClass('fa-caret-down fa-caret-right');
        }
    }
    return '<div class="dragger" data-item="' + item_data.item + '"><i class="fa fa-file-o"></i><h4>' + item_data.title + '</h4><span class="selected_nums">' + counts + '</div></div>';
}

function drop(event, ui) {
    console.log(event);
    var item_data = event.target.dataset.id;
    Buckty.MoveFiles(item_data, 1);
}

Buckty.Order = function (order, da) {
    Buckty.setCookie('order', order, 1000);
    Buckty.setCookie('order_in', da, 1000);
    Buckty.handler('', window.location.href);
}

Buckty.mouseEvent = function (e) {
    jQuery('.drop_down').removeClass('open');
    jQuery('.drop_m').removeClass('active');
    Buckty.ItemInSelection();
    jQuery('.context').removeClass('open').hide();
}
Buckty.documentEvent = function () {
    jQuery('.search_container .suggest_box').hide();
    jQuery('.notes_container').hide();
    Buckty.ItemInSelection();
}
Buckty.Dropdown = function (e) {
    var j_ = jQuery(this);
    var o_ = j_.data('drop');
    var el_ = jQuery('#' + o_);
    var pos = j_.position();
    jQuery('.drop_down').removeClass('open');
    var width = j_.outerWidth();
    j_.toggleClass('active');
    if (el_.hasClass('open')) {
        el_.removeClass('open');
    } else {
        el_.addClass('open');
        el_.css({
            position: "absolute",
            top: pos.top + 40 + "px",
            left: pos.left - 90 + "px"
        }).show();
    }

    e.stopPropagation();
}
$.ajaxTransport("+binary", function (options, originalOptions, jqXHR) {
    // check for conditions and support for blob / arraybuffer response type
    if (window.FormData && ((options.dataType && (options.dataType == 'binary')) || (options.data && ((window.ArrayBuffer && options.data instanceof ArrayBuffer) || (window.Blob && options.data instanceof Blob))))) {
        return {
            // create new XMLHttpRequest
            send: function (headers, callback) {
                // setup all variables
                var xhr = new XMLHttpRequest(),
                    url = options.url,
                    type = options.type,
                    async = options.async || true,
                // blob or arraybuffer. Default is blob
                    dataType = options.responseType || "blob",
                    data = options.data || null,
                    username = options.username || null,
                    password = options.password || null;

                xhr.addEventListener('load', function () {
                    var data = {};
                    data[options.dataType] = xhr.response;
                    // make callback and send data
                    callback(xhr.status, xhr.statusText, data, xhr.getAllResponseHeaders());
                });

                xhr.open(type, url, async, username, password);

                // setup custom headers
                for (var i in headers) {
                    xhr.setRequestHeader(i, headers[i]);
                }

                xhr.responseType = dataType;
                xhr.send(data);
            },
            abort: function () {
                jqXHR.abort();
            }
        };
    }
});
Buckty.loadNotes = function () {
    var noteBox = $('.notes_container');
    var container = $('.notes_container .notification_box');

    container.empty();
    container.append('<div class="loader"></div>');
    noteBox.show();
    $.getJSON(site_url + '/useraction/notes', {
        csrf_Buckty: Buckty.Buckty_getToken,
        t: 'get'
    }).done(function (note) {
        container.find('.loader').remove();
        if (note.length == 0) {
            var n = '<div class="no_notifications"><span class="text">No notifications</span></div>';
            container.append(n);
            return false;
        }
        $.each(note, function () {
            if (this.note.content_type === 'file' || this.note.content_type === 'folder') {
                var n = '<div class="note-item notification_' + this.note.id + '">' +
                    '<div class="icon_note">' +
                    '<i class="fa fa-' + this.note.content_type + '-o"></i>' +
                    '</div>' +
                    '<div class="det_note">' +
                    '<span class="main_t">' + this.user.name + ' - ' + this.note.body + '</span>' +
                    '</div>' +
                    '<div class="actions">' +
                    '<a onclick="Buckty.removeNote(\'' + this.note.id + '\');" class="act"><i class="fa fa-remove"></i></a>' +
                    '</div>' +
                    '</div>';
            } else {
                var n = '<div class="note-item notification_' + this.note.id + '">' +
                    '<div class="icon_note">' +
                    '<i class="fa fa-' + this.note.content_type + '"></i>' +
                    '</div>' +
                    '<div class="det_note">' +
                    '<span class="main_t">' + this.note.body + '</span>' +
                    '</div>' +
                    '<div class="actions">' +
                    '<a onclick="Buckty.removeNote(\'' + this.note.id + '\',\'single\');" class="act"><i class="fa fa-remove"></i></a>' +
                    '</div>' +
                    '</div>';
            }
            container.append(n);
        });
    });
    return false;
}

Buckty.checkNotes = function () {
    var have_note = $('.have_note');
    $.getJSON(site_url + 'useraction/checkn', {csrf_Buckty: Buckty.Buckty_getToken}).done(function (d) {
        if (d.notes_count > 0) {
            Buckty.toast('bell', d.message);
            Buckty.handler('', location.href);
        }

        if (d.notes_unread > 0) {
            have_note.show();
        } else {
            have_note.hide();
        }
    });
}

Buckty.removeNote = function (id, term) {
    if (term === 'single') {
        $('.notification_' + id).css('opacity', 0.5);
    }
    $.post(site_url + 'useraction/deleten', {
        csrf_Buckty: Buckty.Buckty_getToken,
        id: id,
        term: term
    }).done(function (r) {
        r = JSON.parse(r);
        if (r.error_code === 0) {
            if (term === 'single') {
                $('.notification_' + id).slideUp().remove();
            } else if (term === 'multi') {
                Buckty.loadNotes();
            }
            Buckty.toast('check-circle-o', r.message);
        } else if (r.error_code === 1) {
            Buckty.toast('exclamation-triangle', r.message);
        }
    });
}

var selected_context_item = '';

Buckty.Context = function (e) {
    Buckty.loading('s');
    e.preventDefault();
    e.stopPropagation();
    var menu = $('.context');
    menu.hide().removeClass('open');
    var pageX = e.pageX;
    var pageY = e.pageY;
    menu.css({
        top: pageY,
        left: pageX
    });

    var mwidth = menu.width();
    var mheight = menu.height();
    var screenWidth = $(window).width();
    var screenHeight = $(window).height();

    var selector = $('.ui-selected');
    var selector_length = selector.length;
    //if window is scrolled
    var scrTop = $(window).scrollTop();

    //if the menu is close to right edge of the window
    if (pageX + mwidth > screenWidth) {
        menu.css({
            left: pageX - mwidth,
            position: 'absolute'
        });
    }

    //if the menu is close to bottom edge of the window
    if (pageY + mheight > screenHeight + scrTop) {
        menu.css({
            top: pageY - mheight,
            position: 'absolute'
        });
    }
    if (selector_length <= 1 || selector_length === 0) {
        jQuery('.files_ > div').removeClass('ui-selected'), jQuery(this).addClass('ui-selected');
    }
    var object = $(this);
    var item = jQuery(this).data('id');
    var type = jQuery(this).data('item-type');
    var hash = item + '/' + type;
    $.get(site_url + 'useraction/context', {csrf_Buckty: Buckty.Buckty_getToken, item: hash}).done(function (r) {
        var r = JSON.parse(r);
        item_data = r;
        var datain = object.data('in');
        var viewin = jQuery('.files_').attr('data-view');
        var starAction = object.hasClass('starred') ? tran.Remove_Star.trans : tran.Add_star.trans;
        var shared = object.hasClass('shared_item') ? 1 : 0;
        var copylink = object.attr('data-shared-link');
        selected_context_item = {item: item, type: type, hash: hash, shared_link: copylink};
        if (datain === 'n' || datain === 's') {
            var json = [{
                name: tran.Preview.trans,
                action: 'preview',
                icon: 'fa fa-eye',
                class: 'preview-file drp-li'
            }, {
                name: tran.Details.trans,
                action: 'details',
                icon: 'fa fa-info',
                class: 'details-file drp-li'
            }, {
                name: tran.Move.trans,
                action: 'move',
                icon: 'fa fa-folder-o',
                class: 'move-file drp-li'
            }, {
                name: tran.Save_to_folder.trans,
                action: 'save_to',
                icon: 'fa fa-cloud icon_blue',
                class: 'move-file drp-li'
            }, {
                name: starAction,
                action: 'star',
                icon: 'fa fa-star-o',
                class: 'star-file drp-li'
            }, {
                name: tran.Share.trans,
                action: 'share',
                icon: 'fa fa-send',
                class: 'share-file drp-li'
            },
                {
                    name: tran.Copy_Link.trans,
                    action: 'copy_link',
                    icon: 'fa fa-link',
                    class: 'copy-link drp-li'

                },
                {
                    name: 'Upload to',
                    action: 'upload_to',
                    icon: 'fa fa-upload',
                    class: 'upload-to drp-li',
                    submenu_in: [{
                        name: tran.Dropbox_it.trans,
                        action: 'dropbox_share',
                        icon: 'fa fa-dropbox',
                        class: 'dropbox-file drp-li'
                    }, {
                        name: tran.Google_Drive.trans,
                        action: 'gdrive_share',
                        icon: 'fa fa-google',
                        class: 'google-file drp-li'
                    }]
                },
                {
                    name: tran.More.trans,
                    action: 'no',
                    icon: 'fa fa-ellipsis-v',
                    calss: 'more-opt drp-li',
                    submenu_in: [{
                        name: tran.Copy.trans,
                        action: 'copy_file',
                        icon: 'fa fa-clone',
                        class: 'copy-file drp-li'
                    }]
                }, {
                    name: tran.Rename.trans,
                    action: 'rename',
                    icon: 'fa fa-i-cursor',
                    class: 'rename-file drp-li'
                }, {
                    name: tran.Download.trans,
                    action: 'download',
                    icon: 'fa fa-download',
                    class: 'download-file drp-li'
                }, {
                    name: tran.Delete.trans,
                    action: 'delete',
                    icon: 'fa fa-trash',
                    class: 'trash-file drp-li'
                }

            ];
        } else if (datain === 't') {
            var json = [{
                name: 'Restore',
                action: 'restore',
                icon: 'fa fa-refresh',
                class: 'move-file drp-li'
            }, {
                name: tran.Delete.trans,
                action: 'delete_f',
                icon: 'fa fa-pencil',
                class: 'share-file drp-li'
            }];
        } else if (datain == undefined && viewin != '2') {
            var json = [{
                name: tran.Create_Folder.trans,
                action: 'create_folder',
                icon: 'fa icon_orange fa-folder-o',
                class: 'create-folder drp-li'
            }, {
                name: tran.Upload_Files.trans,
                action: 'upload_files',
                icon: 'fa icon_blue fa-plus',
                class: 'upload_files drp-li'
            }, {
                name: tran.Select_All.trans,
                action: 'select_all',
                icon: 'fa icon_blue fa-check-square',
                class: 'select-all drp-li'
            }, {
                name: tran.Reload.trans,
                action: 'reload',
                icon: 'fa fa-refresh',
                class: 'reload-location drp-li'
            },];
        } else if (datain == undefined && viewin === '2') {
            var json = [{
                name: tran.Select_All.trans,
                action: 'select_all',
                icon: 'fa icon_blue fa-check-square',
                class: 'select-all drp-li'
            }];
        }
        $('.context ul').empty();
        $('.context_absolute > ul').empty();
        $.each(json, function () {
            var class_property = '';
            var in_action = this.action;
            if (
                (this.action === 'preview' && type === 'folder') ||
                (this.action === 'move' && r.owner === '0') ||
                (this.action === 'rename' && r.permission === '2') ||
                (this.action === 'copy_file' && type === 'folder')
            ) {
                in_action = 'none';
                class_property = 'class="disabled"';
            }
            if ((this.action === 'save_to' && r.owner === '1')) {
                return true;
            }
            var sub_ico = '';
            var sub = '';
            if (this.hasOwnProperty('submenu_in')) {
                sub_ico = '<i class="fa fa-angle-right"></i>';
                var undermenu = '';
                $.each(this.submenu_in, function (i, subm) {
                    var class_child_property = '';
                    var in_child_action = subm.action;
                    if (
                        (subm.action == 'dropbox_share' && site_info.dropbox.activation == '0') ||
                        (subm.action == 'gdrive_share' && site_info.google.drive_activation == '0') ||
                        (this.action === 'copy_file' && type === 'folder')
                    ) {
                        in_child_action = 'none';
                        class_child_property = 'class="disabled"';
                    }
                    undermenu = undermenu + ' <li ' + class_child_property + '><a href="javascript:void(0);" data-action="' + in_child_action + '" class="' + subm.class + '"><i class="' + subm.icon + '"></i><span class="side_text">' + subm.name + '</span></a></li>';
                });
                sub = '<ul class="submenu">' + undermenu + '</ul>';
            }
            var m = '<li ' + class_property + '><a href="javascript:void(0);" data-action="' + in_action + '" class="' + this.class + '"><i class="' + this.icon + '"></i><span class="side_text">' + this.name + '</span>' + sub_ico + '</a>' + sub + '</li>';
            $('.context > ul').append(m);
        });
        menu.addClass('open').show();
        Buckty.loading('h');
    });
}

jQuery(document.body).on('click', '.context a', function () {
    $('.context').hide();
    var selector = $('.ui-selected');
    var selector_length = selector.length
    var action = $(this).attr('data-action');
    var item = selected_context_item.item;
    var type = selected_context_item.type;
    var title = $('.item_' + item).attr('data-title');
    var copylink = selected_context_item.shared_link;
    switch (action) {
        case 'delete':
            var num = $('.files_ .ui-selected').length;
            if (num > 1) {
                Buckty.removeMulti();
            } else {
                Buckty.removeItem(item + '/' + type, 'single');
            }
            break;
        case 'move':
            Buckty.MoveFiles(0, 0);
            break;
        case 'save_to':
            Buckty.MoveFiles(item + '/' + type, 0);
            break;
        case 'rename':
            Buckty.RenameBox(item, type, title);
            break;
        case 'restore':
            Buckty.restore();
            break;
        case 'create_folder':
            Buckty.folder_create();
            break;
        case 'upload_files':
            Buckty.uploadSelect();
            break;
        case 'delete_f':
            Buckty.deletePerma(item + '/' + type, 'single');
            break;
        case 'select_all':
            Buckty.SelectAll();
            break;
        case 'download':
            if (selector_length > 1) {
                Buckty.DownloadMulti();
            } else if (type === 'folder') {
                Buckty.DownloadMulti(item, type);
            } else {
                Buckty.DownloadSingle(item, type);
            }
            break;
        case 'copy_link':
            Buckty.Copy(item + '/' + type, 'no');
            break;
        case 'share':
            Buckty.Share(item, type);
            break;
        case 'dropbox_share':
            Buckty.DropboxPush(item, type);
            break;
        case 'gdrive_share':
            Buckty.GdrivePush(item, type);
            break;
        case 'star':
            Buckty.ItemStar(item, type);
            break;
        case 'details':
            Buckty.details(item, type);
            break;
        case 'preview':
            Buckty.preview(item, type);
            break;
        case 'reload':
            Buckty.Reload();
            break;
        case 'copy_file':
            Buckty.copyItem(item);
            break;
    }
});

Buckty.setCookie = function (name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000); // ) removed
        var expires = "; expires=" + date.toGMTString(); // + added
    } else
        var expires = "";
    document.cookie = name + "=" + value + expires + ";path=/"; // + and " added
}
Buckty.removeCookie = function (name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
};
Buckty.popup = function (id, t) {
    var ob = jQuery('#' + id);
    var close_p = 'Buckty.popup(\'' + id + '\',\'c\');';
    if (t == 'c') {
        ob.hide();
        ob.remove();
    } else if (t == 'o') {
        ob.show();
        ob.find('a.close').attr('onclick', close_p);
    }
}
Buckty.TreeView = function (hash) {
    var hash_ = $(this).siblings('a').data('hash');
    if (open_folders.hasOwnProperty(hash_)) {
        delete open_folders[hash_];
    } else {
        open_folders.push(hash_);
    }
    hash = typeof hash !== 'undefined' ? hash_ : hash;
    var parent = $(this).parent('li');
    var viewmove = $(this).data('move');
    $(this).toggleClass('fa-caret-down fa-caret-right');
    $(this).siblings('a').find('i').toggleClass('icon-folder-open icon-folder');
    Buckty.load_script();
    parent.children('ul').toggle();
}

Buckty.SelectAll = function () {
    $('.files_ > div').addClass('ui-selected');
}

Buckty.removeMulti = function () {
    if ($('.files_ .ui-selected').length > 0) {
        var num = $('.files_ .ui-selected').length;
        $.confirm({
            text: num + tran.items_are_being_trashed.trans,
            title: 'Are you sure',
            confirm: function () {
                var items = Buckty.ItemInSelection();
                if ($('.files_').data('view') === 1) {
                    Buckty.removeItem(items, 'multi');
                } else if ($('.files_').data('view') === 2) {
                    Buckty.deletePerma(0, 0);
                }
            },
            cancel: function () {

            }
        });

        return true;
    }
    Buckty.toast('exclamation-triangle', tran.Select_the_file_folders.trans);
}
Buckty.restore = function () {
    var items = Buckty.ItemInSelection();
    $.post(site_url + 'useraction/restore', {
        hash: items,
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (d) {
        var d = JSON.parse(d);
        if (d.error_code === 1) {
            Buckty.toast('exclamation-triangle', d.message);
        } else if (d.error_code === 0) {
            $.each(items, function () {
                var hash = this.split('/');
                $('.item_' + hash[0]).remove();
            });
            Buckty.toast('check-circle-o', d.message);
        }
        Buckty.loadTree();
    });
}
Buckty.Buckty_getToken = function () {
    return csrf_token;
}
Buckty.getCurrentin = function () {
    return window['current_folder'];
}
Buckty.details = function (item, type) {
    var container = $('#item_preview');
    container.empty().append('<div class="loader"></div>').show();
    var hash_i = item + '/' + type;
    $.get(site_url + 'useraction/itemdetails', {
        csrf_Buckty: Buckty.Buckty_getToken,
        hash: hash_i
    }).done(function (data) {
        $('#item_preview .loader').remove(), container.append(data);
    })
}
Buckty.preview = function (item, type) {
    var hash_i = item + '/' + type;
    $.get(site_url + 'useraction/itempreview', {
        csrf_Buckty: Buckty.Buckty_getToken,
        hash: hash_i
    }).done(function (data) {
        $('body').append(data);
        $('video,audio').mediaelementplayer();
    })
}
Buckty.Share = function (item, type) {
    var hash_i = item + '/' + type;
    $.get(site_url + 'useraction/share', {
        hash: hash_i,
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (v) {
        $('body').append(v);
        Buckty.popup('js_share', 'o');
        $('.add_user').click(function () {
            $(this).siblings('.input_container').show();
        });
        $('.add_people').click(function () {
            $(this).parents('.modal_content').find('.shared_users').slideToggle();
            if ($(this).parents('.modal_content').find('.email_item').is(':visible')) {
                $(this).parents('.modal_content').find('.email_item').slideToggle();
            }
            if ($(this).parents('.modal_content').find('.add_password').is(':visible')) {
                $(this).parents('.modal_content').find('.add_password').slideToggle();
            }
        });
        $('.send_email').click(function () {
            $(this).parents('.modal_content').find('.email_item').slideToggle();
            if ($(this).parents('.modal_content').find('.shared_users').is(':visible')) {
                $(this).parents('.modal_content').find('.shared_users').slideToggle();
            }
            if ($(this).parents('.modal_content').find('.add_password').is(':visible')) {
                $(this).parents('.modal_content').find('.add_password').slideToggle();
            }
        });
        $('.add_pass').click(function () {
            $(this).parents('.modal_content').find('.add_password').slideToggle();
            if ($(this).parents('.modal_content').find('.shared_users').is(':visible')) {
                $(this).parents('.modal_content').find('.shared_users').slideToggle();
            }
            if ($(this).parents('.modal_content').find('.email_item').is(':visible')) {
                $(this).parents('.modal_content').find('.email_item').slideToggle();
            }
        });
        $('select').niceSelect();
        Buckty.SuggestUsers.suggest();
        $("#emailTags").tagit({
            placeholderText: 'Email addresses',
            beforeTagAdded: function (event, ui) {
                return Buckty.isEmail(ui.tagLabel);
            }
        });
    });
}

Buckty.addPassword = function (e) {
    $('.field.bt .loader').show();
    var data = e.serializeArray();
    data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
    $.post(site_url + 'useraction/additempassword', data).done(function (r) {
        r = JSON.parse(r);
        if (r.error_code === 0) {
            Buckty.toast('check-circle-o', r.message);
        } else {
            Buckty.toast('exclamation-triangle', r.message);
        }
        $('.field.bt .loader').hide();
    });
    return false;
}
Buckty.checkPassword = function (e) {
    var data = e.serializeArray();
    $.post(location.href, data).done(function (r) {
        try {
            var response = JSON.parse(r);
            var error_container = $('.field .info');
            if (response.error_code === 1) {
                error_container.addClass('in_error').text(response.message);
            }
        } catch (e) {
            var container = $('.viewer_container');
            var content = $(r).filter('.viewer_container').html();
            container.html(content);
        }
    });
    return false;
}

Buckty.email = function (e) {
    Buckty.loading('s');
    var data = e.serializeArray();
    data.push({name: 'csrf_Buckty', value: Buckty.Buckty_getToken});
    $.post(site_url + 'useraction/email', data).done(function (r) {
        r = JSON.parse(r);
        if (r.error_code === 0) {
            $(e)[0].reset();
            Buckty.popup('js_share','c');
            Buckty.toast('check-circle-o', r.msg.message);
        } else if (r.error_code === 1) {
            Buckty.toast('exclamation-triangle', r.msg.message);
        } else {
            Buckty.toast('exclamation-triangle', tran.Something_went_wrong.trans);
        }
        Buckty.loading('h');
    });
    return false;
}

Buckty.isEmail = function (email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

Buckty.SuggestUsers = {
    suggest: function () {
        $('#addUserstoitem').on('keyup', function () {
            var key = $(this).val();
            if (key.length == 0) {
                $('.add_user_form .suggestion').hide().find('ul').empty();
                return true;
            }
            $.get(site_url + 'useraction/getusers', {
                action: 'getUsers',
                key: key,
                csrf_Buckty: Buckty.Buckty_getToken
            }).done(function (u) {
                var u = JSON.parse(u);
                var container = $('.add_user_form .suggestion');
                var top = $('.add_user_form .field.input_tags').height() + 20;
                container.find('ul').empty();
                if (u) {
                    $.each(u, function (i, usr) {
                        if ($('.user_' + usr.hash).length) {
                            return true;
                        }
                        if ($('.suggested_' + usr.hash).length) {
                            return true;
                        }
                        var user = '<li><a data-image="' + usr.profile_pic.medium + '" data-name="' + usr.name + '" data-id="' + usr.hash + '" onclick="Buckty.SuggestUsers.append($(this));"><span class="username">' + usr.name + '</span></a></li>';
                        container.show().find('ul').prepend(user);
                    });
                } else {
                    container.show().find('ul').html('<li><span class="not_found">'+tran.No_users_were_found.trans+'</span></li>');
                }
                container.css('top', top + 'px');
            });
        });
    },
    append: function (e) {
        $('#addUserstoitem').val('');
        var container = $('.add_user_form .field.input_tags');
        var image = e.attr('data-image');
        var name = e.attr('data-name');
        var id = e.attr('data-id');

        var user = '<div class="suggested_' + id + ' user_share">' +
            '<div class="icon">' +
            '<img src="' + image + '" alt="' + name + '">' +
            '</div>' +
            '<span class="username">' + name + '</span>' +
            '<i class="fa fa-close" data-id="' + id + '" onclick="Buckty.SuggestUsers.remove($(this))"></i>' +
            '</div>';
        var input = '<input type="hidden" name="user[]" class="suggested_' + id + '" value="' + id + '"/>';
        container.prepend(user);
        container.prepend(input);
        $(document).click(function () {
            var suggest_box = $('.add_user_form .suggestion');
            suggest_box.hide().find('ul').empty();
        });
    },
    remove: function (e) {
        var id = e.attr('data-id');
        var container = $('.add_user_form .field.input_tags');
        container.find('.suggested_' + id).remove();
    },
    unlinkUser: function (user, type, item) {
        var hash = item + '/' + type;
        $.post(site_url + 'useraction/unlinkuser',
            {
                csrf_Buckty: Buckty.Buckty_getToken,
                item: hash,
                user: user
            })
            .done(function (e) {
                $('.user_' + user).remove();
                Buckty.handler('', location.href);
            });
    },
    addUsers: function (e) {
        var data = e.serializeArray();
        data.push({
            name: 'csrf_Buckty',
            value: Buckty.Buckty_getToken
        });
        $.post(site_url + 'useraction/itemShare', data).done(function (e) {
            var e = JSON.parse(e);
            if (e.error_code === 0) {
                Buckty.toast('check-circle-o', e.msg.message);
            } else if (e.error_code === 1) {
                Buckty.toast('check-circle-o', e.msg.message);
            }
            Buckty.SuggestUsers.loadShares(e.msg.data.type, e.msg.data.item);
        });
        return false;
    },
    loadShares: function (type, item) {
        var hash_i = item + '/' + type;
        $.get(site_url + 'useraction/share', {
            hash: hash_i,
            csrf_Buckty: Buckty.Buckty_getToken
        }).done(function (v) {
            var share_body = $(v).find('.shared_users').html();
            var container = $('.modal_content .shared_users');
            container.html(share_body);
            $('.add_user').click(function () {
                $(this).siblings('.input_container').show();
            });
            $('.add_people').click(function () {
                $(this).parents('.modal_content').find('.shared_users').slideToggle();
            });
            $('select').niceSelect();
            Buckty.SuggestUsers.suggest();
        });
        Buckty.handler('', location.href);
    },
    changePermission: function (user, type, file, permission) {
        Buckty.loading('s');
        $.post(site_url + 'useraction/changepermission', {
            csrf_Buckty: Buckty.Buckty_getToken,
            u: user,
            i: file,
            t: type,
            p: permission
        }).done(function (r) {
            r = JSON.parse(r);
            if (r.error_code === 1) {
                Buckty.toast('exclamation-triangle', r.message);
            } else if (r.error_code === 0) {
                Buckty.SuggestUsers.loadShares();
                Buckty.toast('check-circle-o', r.message);
            }
        });
    }

}

Buckty.ItemStar = function (item_in, type) {
    var item = item_in + '/' + type;
    var star_selector = $('.star_' + item_in);
    var item_selector = $('.item_' + item_in);
    if (star_selector.hasClass('starred')) {
        $.post(site_url + 'useraction/removestar', {
            hash: item,
            csrf_Buckty: Buckty.Buckty_getToken
        }).done(function (d) {
            var d = JSON.parse(d);
            if (d.error_code === 0) {
                star_selector.removeClass('starred');
                item_selector.removeClass('starred');
                if (item_selector.data('in') == 's') {
                    item_selector.remove();
                }
                Buckty.toast('star-o', d.msg.message);
            } else if (d.error_code === 1) {
                Buckty.toast('exclamation-triangle', d.msg.message);
            }
        });
    } else {
        $.post(site_url + 'useraction/staritem', {
            hash: item,
            csrf_Buckty: Buckty.Buckty_getToken
        }).done(function (d) {
            var d = JSON.parse(d);
            if (d.error_code === 0) {
                star_selector.addClass('starred');
                item_selector.addClass('starred');
                Buckty.toast('star-o', d.msg.message);
            } else if (d.error_code === 1) {
                Buckty.toast('exclamation-triangle', d.msg.message);
            }
        });
    }
}

Buckty.ShareWithUser = function (event, item, type) {
    var container = $('#users_sug');
    var user = event.attr('data-id');
    if ($('li.user_' + user).length) {
        Buckty.toast('exclamation-triangle',tran.Already_Shared_With_this_user.trans);
    } else {
        $.post(site_url + 'useraction/itemShare', {
            hash: item,
            user: user,
            csrf_Buckty: Buckty.Buckty_getToken
        }).done(function (d) {
            var d = JSON.parse(d);
            if (d.error_code === 0) {
                Buckty.toast('check-circle-o', d.msg.message);
                var user = '<li class="user_' + event.attr('data-id') + '">' +
                    '<div class="icon">' +
                    '<i class="fa fa-close" onclick="Buckty.SuggestUsers.unlinkUser(\'' + user + '\',\'' + type + '\',\'' + item + '\');></i>' +
                    '<img src="' + event.attr('data-pic') + '" class="tooltip-top" data-tooltip="' + event.attr('data-name') + '">' +
                    '</div>' +
                    '</li>';
                container.prepend(user);
            } else if (d.error_code === 1) {
                Buckty.toast('exclamation-triangle', d.msg.message);
            }
        });
    }
}
Buckty.deletePerma = function () {
    if (current_folder === '404' || current_folder === undefined) {
        return true;
    }
    var items = Buckty.ItemInSelection();
    $.post(site_url + '/useraction/removeitem', {
        hash: items,
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (data) {
        var data = JSON.parse(data);
        if (data.error_code === 0) {
            Buckty.toast('check-circle-o', data.msg.message);
            $.each(items, function () {
                var hash = this.split('/');
                $('.item_' + hash[0]).remove();
            });
            if ($('.files_').children().length == 0) {
                Buckty.handler('', location.href);
            }
            Buckty.RefreshSpace();
        } else if (data.error_code === 1) {
            Buckty.toast('exclamation-triangle', data.msg.message);
        }
    });
}
Buckty.DropboxPush = function (item, type) {
    var container = $('.item_' + item);
    var item_ = item + '/' + type;
    container.append('<div class="loader"></div>');
    container.removeClass('item_clickable').addClass('uploading');
    $.post(site_url + 'dropbox/push', {
        hash: item_,
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (s) {
        var state = JSON.parse(s);
        $('.item_' + item + ' .loader').remove();
        container.addClass('item_clickable').removeClass('uploading');
        if (state.auth === 0) {
            Buckty.AuthDropbox();
            return false;
        }
        if (state.error_code === 0) {
            Buckty.toast('check-circle-o', state.msg.message);
        } else if (state.error_code === 1) {
            Buckty.toast('exclamation-triangle', state.msg.message);
        }
    });
}

Buckty.GdrivePush = function (item, type) {
    var container = $('.item_' + item);
    var item_ = item + '/' + type;
    container.append('<div class="loader"></div>');
    container.removeClass('item_clickable').addClass('uploading');
    $.post(site_url + 'gdrive/push', {
        hash: item_,
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (s) {
        var state = JSON.parse(s);
        $('.item_' + item + ' .loader').remove();
        container.addClass('item_clickable').removeClass('uploading');
        if (state.msg.auth === 0) {
            Buckty.AuthGdrive();
            return false;
        }
        if (state.error_code === 0) {
            Buckty.toast('check-circle-o', state.msg.message);
        } else if (state.error_code === 1) {
            Buckty.toast('exclamation-triangle', state.msg.message);
        }
    });
}
Buckty.DownloadSingle = function (item, type) {
    if (type === 'folder') {
        Buckty.DownloadMulti();
        return false;
    } else if (type === 'folder_view') {
        Buckty.DownloadMulti(item, type);
        return false;
    }
    var item = item;
    var token = Buckty.Buckty_getToken;
    if ($('#download_item_frame').length) {
        $('#download_item_frame').remove();
    }
    var dlif = $('<iframe/>', {
        'src': site_url + 'useraction/get/' + item,
        'id': 'download_item_frame'
    }).hide();
    $('body').append(dlif);
}
Buckty.DownloadMulti = function (item, type) {
    var f = [];
    if (item === undefined && type === undefined) {
        f = Buckty.ItemInSelection();
    } else {
        f.push(item + '/' + type);
    }
    Buckty.loading('s');
    $.post(site_url + 'useraction/zip', {
        csrf_Buckty: Buckty.Buckty_getToken,
        items: f
    }).done(function (r) {
        var r = JSON.parse(r);
        if (r.zipped === 1) {
            var token = Buckty.Buckty_getToken;
            if ($('#download_item_frame').length) {
                $('#download_item_frame').remove();
            }
            var dlif = $('<iframe/>', {
                'src': site_url + 'useraction/getzip/' + r.name,
                'id': 'download_item_frame'
            }).hide();
            $('body').append(dlif);
        } else if (r.zipped === 0) {
            Buckty.toast('exclamation-triangle', tran.Invalid_Folder.trans);
        }
        Buckty.loading('h');
    });

}

Buckty.RenameBox = function (hash, type, name) {
    var popup = '<div id="js_rename_folder" class="mini_pop">' +
        '<div class="overlay"></div>' +
        '<div class="modal_container">' +
        '<div class="modal">' +
        '<div class="modal_header">' +
        '<h1 class="modal_title">Rename ' + type + '</h1>' +
        '<a href="javascript:void(0);" class="close"><i class="fa fa-remove"></i></a>' +
        '</div>' +
        '<div class="modal_content">' +
        '<form id="rename_form">' +
        '<div class="field">' +
        '<input type="text" name="item_name" placeholder="Rename Item" value="' + name + '" autofocus/>' +
        '</div>' +
        '<div class="buttons_container margin">' +
        '<input type="hidden" name="type" value="' + type + '"/>' +
        '<input type="hidden" name="hash" value="' + hash + '"/>' +
        '<a class="button primary" onclick="Buckty.popup(\'js_rename_folder \',\'c\');">Cancel</a>' +
        '<button class="button blue" type="submit">Rename</button>' +
        '</div>' +
        '</form>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';
    $('body').append(popup);
    Buckty.popup('js_rename_folder', 'o');
    Buckty.RenameItem(hash, type);
}

Buckty.RenameItem = function (hash, type) {
    jQuery('#rename_form').submit(function (e) {
        e.preventDefault();
        var formData = $(this).serializeArray();
        formData.push({
            name: 'csrf_Buckty',
            value: Buckty.Buckty_getToken
        });
        $.post(site_url + 'useraction/renameitem', formData).done(function (d) {
            var d_item = JSON.parse(d);
            var data = d_item.data;
            if (d_item.error_code === 0) {
                var item = $('.item_' + hash);
                Buckty.popup('js_rename_folder', 'c');
                $('#js_rename_folder').remove();
                if (type === 'folder') {
                    item.attr('data-title', data.folder_name);
                    item.find('.title').text(data.folder_name);
                } else {
                    item.attr('data-title', data.file_name);
                    item.find('.title').text(data.file_name);
                }
            } else if (d_item.error_code === 1) {
                Buckty.toast('exclamation-triangle', data.message);
            }
        });
    });
}
Buckty.removeItem = function (item, w) {
    if (w === 'multi') {
        item = item;
    } else if ('single') {
        var files = [];
        files.push(item);
        item = files;
    }
    $.ajax({
        url: site_url + 'useraction/deleteitem',
        type: 'post',
        data: {
            items: item,
            csrf_Buckty: Buckty.Buckty_getToken
        },
        processData: true,
        dataType: 'json',
        statusCode: {
            403: function () {
                Buckty.toast('exclamation-triangle', tran.Access_Forbidden.trans);
            },
            404: function () {
                Buckty.toast('exclamation-triangle', tran.NOT_FOUND.trans);
            }
        },
        success: function (d) {
            if (d.error_code === 0) {
                Buckty.toast('check-circle-o', d.message);
                $.each(item, function (e, i) {
                    var item = i.split('/');
                    if (item[1] === 'folder') {
                        $('#folder_' + item[0]).remove();
                        Buckty.loadTree();
                        $('.item_' + item[1]).remove();
                    } else if (item[1] === 'file') {
                        jQuery('.item_' + item[0]).remove();
                    }
                });
            } else if (d.error_code === 1) {
                Buckty.toast('exclamation-triangle', d.message);
            }

        }
    });
}
Buckty.copyItem = function (item) {
    $.post(site_url + 'useraction/copyitem', {
        csrf_Buckty: Buckty.Buckty_getToken,
        hash: item,
        parent: current_folder
    }).done(function (r) {
        r = JSON.parse(r);
        if (r.error_code === 0) {
            Buckty.toast('check-circle-o', r.msg.message);
            var file = r.msg.file;
            var type = file.file_mime.split('/');
            var size = Buckty.bytes(file.file_size);
            if (type[0] === 'image') {
                var file_view = '<img src="' + site_url + 'userfile/' + file.hash + '?w=200&h=300" class="icon_img ignore" alt="hello">';
            } else {
                var file_view = '<div class="file-icon file-icon-xl" data-type="' + file.file_type + '"></div>';
            }
            var f = '<div id="item_' + file.hash + '" class="item_' + file.hash + ' file_item item_clickable" data-item-type="file" data-in="n" data-title="' + file.file_name + '" data-item="' + file.hash + '" data-id="' + file.hash + '">' +
                '<div class="file_icon ignore">' + file_view + '</div>' +
                '<div class="det ignore">' +
                '<a href="#" class="ignore"><h3 class="title">' + file.file_name + '</h3></a>' +
                '<span class="size ignore"><b>' + tran.Size.trans + ': </b>' + size + '</span>' +
                '<span class="date ignore">' + file.file_date + '</span>' +
                '</div>' +
                '</div>';
            jQuery('.files_').append(f);
            jQuery('.no_files').remove();
            Buckty.load_script();
            Buckty.RefreshSpace();
        } else if (r.error_code === 1) {
            Buckty.toast('check-circle-o', r.msg.message);
        }
    });
}

jQuery(function () {
    jQuery(document.body).on('click', '#removeFiles', Buckty.removeMulti);
    $(window).resize(Buckty.resizeEvent);
    setInterval(function () {
        Buckty.userLog();
    }, 1000 * 60 * 2);
    $.ajaxSetup({
        data: {
            csrf_Buckty: Buckty.Buckty_getToken
        }
    });
    $(document).ajaxComplete(function () {
        // console.clear();
        //console.log('  ');
    });
    $('form').submit(function (e) {
        var formObj = $(this);
        var formURL = formObj.attr("action");
        var callback = formObj.data('func');
        var dataType = formObj.data('type');
        var method = formObj.attr('method');
        var formData = new FormData(this);
        switch (dataType) {
            case 'login':
                $(this).validate({ // initialize the plugin
                    rules: {
                        identity: {
                            required: true
                        },
                        password: {
                            required: true,
                            minlength: 5
                        }
                    }
                });
                if ($(this).valid()) {
                    formObj.find('.loader').show();
                    Buckty.Ajaxlog(formData, formURL, method);
                }
                break;
            case 'Login':
                $(this).validate({ // initialize the plugin
                    rules: {
                        identity: {
                            required: true
                        },
                        password: {
                            required: true,
                            minlength: 5
                        }
                    }
                });
                if ($(this).valid()) {
                    formObj.find('.loader').show();
                    Buckty.Ajaxlog(formData, formURL, method);
                }
                break;
            case 'register':
                $(this).validate({ // initialize the plugin
                    rules: {
                        username: {
                            required: true
                        },
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 5,
                            maxlength: 15
                        },
                        password_confirm: {
                            required: true,
                            minlength: 5,
                            maxlength: 15
                        }
                    }
                });
                if ($(this).valid()) {
                    formObj.find('.loader').show();
                    Buckty.Ajaxlog(formData, formURL, method);
                }
                break;
            case 'reset':
                $(this).validate({ // initialize the plugin
                    rules: {
                        email: {
                            required: true,
                            email: true
                        },
                        password: {
                            required: true,
                            minlength: 5,
                            maxlength: 15
                        },
                        confirm_password: {
                            required: true,
                            minlength: 5,
                            maxlength: 15
                        }
                    }
                });
                if ($(this).valid()) {
                    formObj.find('.find').loader;
                    Buckty.Ajaxlog(formData, formURL, method);
                }
                break;
            default:
                Buckty.AjaxRequest(formData, formURL, method);
        }
        e.preventDefault();
    });
});

/**
 * Raises an ajax request to the server.
 *
 * @param params -
 *            Parameters to the request.
 * @param url -
 *            The url of the request.
 * @param func -
 *            The function to be called back when response is received.
 * @param connType -
 */
Buckty.AjaxRequest = function (params, url, method) {

    $.ajax({
        url: url,
        type: 'POST',
        data: params,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function (data) {
            if (data.refresh == 1) {
                location.reload();
            } else if (data.refresh == undefined && data.code == 1) {
                Buckty.toastlog(data.message, 'error', data.type);
            } else if (data.refresh == undefined && data.code == 2) {
                Buckty.toastlog(data.message, 'success', data.type);
            }
        },
        error: function (error) {
            console.log(error);
        }
    });


}

Buckty.Ajaxlog = function (params, url, method) {

    $.ajax({
        url: url,
        type: 'POST',
        data: params,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        dataType: 'json',
        success: function (data) {
            if (data.refresh == 1) {
                location.reload();
            } else if (data.refresh == undefined && data.code == 1) {
                Buckty.toastlog(data.message, 'error', data.type);
            } else if (data.refresh == undefined && data.code == 2) {
                Buckty.toastlog(data.message, 'success', data.type);
                $('form').trigger("reset");
            }
            $('form').find('.loader').hide();
        },
        error: function (error) {
            Buckty.toastlog('Something Went Wrong', 'error', 'log');
        }
    });


}

Buckty.UrlParams = function () {
    var params = {};
    window.location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (str, key, value) {
        params[key] = value;
    });

    return params;
}

Buckty.element = function (id) {
    var element = document.getElementById(id);
    return element;
}

/**
 * Creates an element with the given html code.
 *
 * @param html - The html code inside the html.
 * @param type - Type - div, span etc.
 * @returns
 */
Buckty.createElement = function (html, type) {
    var element = document.createElement(type);
    element.innerHTML = html;
    return element;
}

/**
 * Loads a particular URL.
 * @param url - The
 * @param page -  A new page (_blank), parent page (_self).
 */
Buckty.loadURL = function (url, page) {
    url = unescape(url);
    window.open(url, "_self", false);
};

/**
 * Get full Url
 */
Buckty.fullUrl = function () {
    return window.location.href;
}


/**
 * Get full Url with path
 */
Buckty.pathUrl = function () {
    return window.location.pathname;
    ; // Returns full URL
}

Buckty.toast = function (i, c) {
    var num = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
    ;
    var container = jQuery('#toast-container');
    container.append('<div id="notification-' + num + '"><i class="fa fa-' + i + '"></i><span class="text">' + c + '</span></div>');
    setTimeout(function () {
        jQuery('#notification-' + num).animate({bottom: '-1000px'}, 3000, function () {
            jQuery('#notification-' + num).remove();
        });
    }, 5000);
}

Buckty.toastlog = function (c, t, w) {
    var num = Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 5);
    ;
    switch (w) {
        case 'log':
            var container = jQuery('#toast-container-log');
            break;
        case 'reg':
            var container = jQuery('#toast-container-reg');
            break;
        default:
            var container = jQuery('#toast-container');
    }

    container.html('<div id="notification-' + num + '" class="' + t + '">' + c + '</div>');
}

Buckty.loading = function (t) {
    switch (t) {
        case 's':
            jQuery('.toast_container > .loading').show().animate({bottom: '0px'}, 100);
            break;
        case 'h':
            jQuery('.toast_container > .loading').animate({bottom: '-300px'}, 100).hide();
            ;
            break;
    }
}

Buckty.logout = function () {
    Buckty.loading('s');
    $.ajax({
        url: site_url + 'logout',
        type: 'post',
        success: function (data) {
            Buckty.loading('h');
            location.reload(site_url);
        }
    });
}

Buckty.user_pic = function () {
    $.ajax({
        url: site_url + 'loadpopup/?pop=profile_pic',
        type: 'post',
        beforeSend: function () {
            Buckty.loading('s');
        },
        success: function (view) {
            jQuery('body').append(view);
            var img = $('<img id="popup_current_pic">');
            img.attr('src', userData.profile_pic.medium);
            img.appendTo('#js_profile_pic .image_container');
            $('.profile_pic_img').attr('src', userData.profile_pic.medium);
            Buckty.popup('js_profile_pic', 'o');
            Buckty.loading('h');
        }
    });
}
Buckty.folder_create = function () {
    $.ajax({
        url: site_url + 'loadpopup/?pop=create_folder',
        type: 'POST',
        beforeSend: function () {
            Buckty.loading('s');
        },
        success: function (view) {
            jQuery('body').append(view);
            Buckty.popup('js_create_folder', 'o');
            Buckty.loading('h');
            Buckty.folderSubmit();
        }
    });
}

Buckty.profile_u = function (file) {
    var form_data = new FormData(); // Creating object of FormData class
    form_data.append("user_image", file); // Appending parameter named file with properties of file_field to form_data
    form_data.append("user_id", userData.id); // Adding extra parameters to form_data
    form_data.append('csrf_Buckty', csrf_token);
    $.ajax({
        url: site_url + "useraction/update_pic",
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        mimeType: "multipart/form-data",
        data: form_data, // Setting the data attribute of ajax with file_data
        type: 'POST',
        beforeSend: function () {
            jQuery('#js_profile_pic .image_container').addClass('loading');
            var loader = $('<div class="loader"></div>');
            loader.appendTo('#js_profile_pic .image_container');
        },
        success: function (data) {
            jQuery('#js_profile_pic .image_container').removeClass('loading');
            jQuery('#js_profile_pic .image_container').find('.loader').remove();
            if (data.image !== undefined) {
                jQuery('#popup_current_pic').attr('src', data.image);
                jQuery('.profile_menu .profile_pic').attr('src', data.image);
            } else {
                Buckty.toast('exclamation-triangle', data.msg);
            }
        }
    });
}

Buckty.uploader = function () {
    var settings = {
        url: site_url + "useraction/uploadfiles",
        method: "POST",
        fileName: "useractionfile",
        multiple: true,
        dragDrop: true,
        returnType: 'json',
        maxFileSize: site_info.max_size,
        onSelect: function (files) {

            var file_size = files[0].size;
            var total = file_size + UserUsedSpace;
            if (file_size > site_info.max_size) {
                Buckty.toast('exclamation-triangle', tran.File_too_large.trans);
                return false;
            }
            if (total > site_info.upload_limit) {
                Buckty.toast('exclamation-triangle', tran.Not_enough_space.trans);
                return false;
            }
            return true; //to allow file submission.
        },
        afterUploadAll: function (d) {

        },
        onSuccess: function (files, status, xhl) {
            console.log(status);
            if (status.error_code === 1) {
                Buckty.toast('exclamation-triangle', status.msg.message);
                jQuery('.ajax-file-upload-bar').css('background', '#a30');
            } else {
                Buckty.toast('check-circle-o', files);
                var file = status.msg.file;
                var type = file.file_mime.split('/');
                var size = Buckty.bytes(file.file_size);
                if (type[0] === 'image') {
                    var file_view = '<img src="' + site_url + 'userfile/' + file.hash + '?w=200&h=300" class="icon_img ignore" alt="hello">';
                } else {
                    var file_view = '<div class="file-icon file-icon-xl" data-type="' + file.file_type + '"></div>';
                }
                var f = '<div id="item_' + file.hash + '" class="item_' + file.hash + ' file_item item_clickable" data-item-type="file" data-in="n" data-title="' + file.file_name + '" data-item="' + file.hash + '" data-id="' + file.hash + '">' +
                    '<div class="file_icon ignore">' + file_view + '</div>' +
                    '<div class="det ignore">' +
                    '<a href="#" class="ignore"><h3 class="title">' + file.file_name + '</h3></a>' +
                    '<span class="size ignore"><b>' + tran.Size.trans + ': </b>' + size + '</span>' +
                    '<span class="date ignore">' + file.file_date + '</span>' +
                    '</div>' +
                    '</div>';
                if($('.files_').data('view') == 1) {
                    jQuery('.files_').append(f);
                    jQuery('.no_files').remove();
                }
                Buckty.load_script();
                Buckty.RefreshSpace();
            }

        },
        onError: function (files, status, errMsg) {
            Buckty.toast('exclamation-triangle', files);
        }
    }
    $("#js_uploader").uploadFile(settings);

}
Buckty.bytes = function (bytes) {
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}
Buckty.folderSubmit = function () {
    jQuery('#js_submit_folder').submit(function (e) {
        e.preventDefault();
        var data = $(this).serializeArray();
        var folderin = Buckty.getCurrentin();
        data.push({
            name: 'user_id',
            value: userData.id
        }, {
            name: 'folder_in',
            value: folderin
        }, {
            name: 'csrf_',
            value: Buckty.Buckty_getToken
        });
        $.ajax({
            url: site_url + "useraction/createfolder",
            dataType: 'json',
            data: data, // Setting the data attribute of ajax with file_data
            type: 'POST',
            beforeSend: function () {
                var loader = jQuery('<div class="loader"></div>');
                loader.appendTo('.modal_container .buttons_container');
            },
            success: function (data) {
                if (data.error_code === 1) {
                    $('.modal_container .buttons_container .loader').remove();
                    Buckty.toast('exclamation-triangle', data.message);
                } else {
                    var folder = '<div id="folder_' + data.folder_hash + '" class="file_item item_'+data.folder_hash+' folder_ item_clickable" data-item-type="folder" data-in="n" data-title="' + data.folder_name + '" data-item="' + data.folder_hash + '" data-id="' + data.folder_hash + '">' +
                        '<div class="file_icon ignore">' +
                        '<i class="icon_i icon-folder  ignore"></i>' +
                        '</div>' +
                        '<div class="det ignore">' +
                        '<a href="/folders/' + data.folder_hash + '" class="ignore"><h3 class="title ignore">' + data.folder_name + '</h3></a>' +
                        '<span class="size ignore"><b>Size: </b>0.00B</span>' +
                        '<span class="date ignore">' + data.date + '</span>' +
                        '<div class="actions ignore">' +
                        '<ul class="ignore"><li class="ignore">' +
                        '<a onclick="Buckty.ItemStar(\'' + data.folder_hash + '\',\'folder\');" class="star_' + data.folder_hash + ' star icon_link ignore"><i class="fa fa-star ignore"></i></a>' +
                        '</li>' +
                        '</ul>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    if($('.files_').data('view') == 1) {
                        jQuery('.files_').prepend(folder);
                        jQuery('.no_files').remove();
                    }
                    Buckty.toast('folder-o', data.message + ' | ' + data.folder_name);
                    Buckty.popup('js_create_folder', 'c');
                    Buckty.load_script();
                    Buckty.loadTree();
                }
            }
        });
    });
}
Buckty.uploaderQueue = function () {
    jQuery('.uploader_queue').show();
}

Buckty.uploadSelect = function () {
    jQuery('#ajax-upload-select').click();
}
Buckty.ItemInSelection = function () {
    var inSelection = [];
    inSelection = [];
    jQuery('.file_item.ui-selected').each(function () {
        inSelection.push($(this).data('item') + '/' + $(this).data('item-type'));
    });
    return inSelection;
}

Buckty.MoveFiles = function (hash, s) {
    Buckty.ItemInSelection();
    var files = Buckty.ItemInSelection();
    if (files) {
        var f = files;
        if (s === 1) {
            $.ajax({
                url: site_url + 'useraction/moveitem',
                type: 'post',
                dataType: 'json',
                data: {
                    hash: hash,
                    files_hash: f,
                    csrf_Buckty: Buckty.Buckty_getToken
                },
                success: function (data) {
                    var type = data.error_code === 0 ? 'check-circle-o' : 'exclamation-triangle';
                    if (data.error_code === 0) {
                        Buckty.toast(type, data.message);
                        if (hash !== current_folder) {
                            $.each(f, function (k, d) {
                                var hash = d.split('/');
                                $('.item_' + hash[0]).remove();
                            });
                        } else {
                            Buckty.handler('', location.href);
                        }
                        Buckty.popup('js_move_folder', 'c');
                        Buckty.RefreshSpace();
                        Buckty.loadTree();
                        Buckty.load_script();
                    } else if (data.error_code === 1) {
                        Buckty.toast(type, data.message);
                    }
                }
            });
        } else if (s === 0) {
            $.ajax({
                url: site_url + 'folderList',
                type: 'post',
                dataType: 'html',
                data: {
                    items: f
                },
                success: function (v) {
                    jQuery('body').append(v);
                    Buckty.popup('js_move_folder', 'o');
                    var fol = '';
                    jQuery('.moveable').on('click', function (e) {
                        fol = $(this).data('id');
                        $('.folder_').removeClass('ui-state-active');
                        $(this).addClass('ui-state-active');
                        $('#moveButton').prop('disabled', false);
                    });
                    jQuery('#moveButton').on('click', function () {
                        Buckty.MoveFiles(fol, 1);
                    });
                }
            });
        }
    } else {
        Buckty.toast('exclamation-triangle', tran.Select_the_file_folders.trans);
    }
}

Buckty.userLog = function () {
    $.ajax({
        url: site_url + 'buckty/login',
        type: 'POST',
        dataType: 'json',
        success: function (d) {
            if (d.login === 'true') {
                jQuery('meta[name="buckty_token"]').attr('content', d.csrf);
            } else if (d.login === 'false') {
                if (jQuery('#js_login_again').legnth) {
                    Buckty.popup('js_login_again', 'o');
                    jQuery('meta[name="buckty_token"]').attr('content', d.csrf);
                } else {
                    $.ajax({
                        url: site_url + 'loadpopup/?pop=loginagain',
                        type: 'post',
                        dataType: 'html',
                        success: function (data) {
                            var popup = $(data).filter('#js_login_again');
                            jQuery('body').append(popup);
                            Buckty.popup('js_login_again', 'o');
                            Buckty.loading('h');

                        }
                    });
                }
            }
        }
    });
}

Buckty.csrfTokenUpdate = function () {
    $.ajax({
        url: site_url,
        type: 'get',
        dataType: 'html',
        success: function (t) {
            var main_script = $(t).filter('#js_main_objects').html();
            $("<script id='#js_main_objects'/>").text(main_script).appendTo("head");
        }
    });
}

Buckty.UploaderEmpty = function () {
    var container = $('.uploader_queue');
    $('.ajax-file-upload-statusbar').remove();
}
Buckty.Copy = function (c, t) {
    if (t === 'target') {
        c = $('#' + c).val();
    } else if (t === 'no') {
        item = c.split('/');
        c = site_url + 'shared/' + item[1] + '/' + item[0];
    }
    clipboard.copy(c)
        .then(
            function () {
                Buckty.toast('check-circle-o',tran.Copied_to_clipboard.trans);
            },
            function (err) {
                Buckty.toast('exclamation-triangle', err);
            }
        );
}

$(function () {
    Buckty.RefreshSpace();
    Buckty.CurrentPage();
    Buckty.checkNotes();
    var w_size = $(window).width();
    var total = w_size - 250;
    $('.main_block').css('width', total);
    $(window).resize(function () {
        var w_size = $(window).width();
        var total = w_size - 250;

        $('.main_block').css('width', total);
    });
    setInterval(Buckty.checkNotes, 5000);
    $('.files_container .files_').multiSelect({
        unselectOn: 'body',
        keepSelection: true,
        selected: 'ui-selected'
    });
    jQuery(document).on('keyup',function(e) {
        if (e.keyCode == 27) {
            if($('.viewer_container.popup').length){
                $('.viewer_container.popup').remove();
            }
            if($('.mini_pop').length){
                $('.mini_pop').remove();
            }
        }
    });
});

Buckty.RefreshSpace = function () {
    $('.userspace .text').text('Calculating...');
    $.get(site_url + 'buckty/userspace', {
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (s) {
        var space = JSON.parse(s);
        $('.userspace .text').text(tran.Memory_used_label.trans+' - '+space.occupied);
        UserUsedSpace = space.occupied_bytes;
        if (space.occupied_p > '85') {
            $('.userspace .loaded_seek').animate({
                width: space.occupied_p + '%'
            }).css('background', '#a30');
        } else {
            $('.userspace .loaded_seek').animate({
                width: space.occupied_p + '%'
            }).css('background', '#24BE12');
        }
    });
}

Buckty.Search = function (event) {
    var container = $('.search_container .suggest_box .container_list ul');
    if (event.length > 3) {
        $.get(site_url + 'search', {
            csrf_Buckty: Buckty.Buckty_getToken,
            s: event
        }).done(function (data) {
            var data = JSON.parse(data);
            var folders = data.folders;
            var files = data.files;
            if (jQuery.isEmptyObject(files) && jQuery.isEmptyObject(folders)) {
                var error = '<li><span class="notice_text">No Files/Folders</span></li>';
                container.empty();
                container.append(error);
            } else {
                container.empty();
                $.each(folders, function () {
                    var folder = '<li><a href="' + site_url + 'folders/' + this.folder_hash + '" class="list_item"><i class="icon_blue icon-folder"></i><span class="item_title">' + this.folder_name + '</span></a></li>';
                    container.append(folder);
                });
                $.each(files, function () {
                    var folder = '<li><a onclick="Buckty.preview(\'' + this.hash + '\',\'file\')" class="list_item"><div class="file-icon ignore" data-type="' + this.file_type + '"></div><span class="item_title">' + this.file_name + '</span></a></li>';
                    container.append(folder);
                });
            }
            container.parents('.suggest_box').show();
            //loader.hide();
        });
    } else {
        container.parents('.suggest_box').hide();
    }
    return false;
}

Buckty.AuthGdrive = function () {
    $.oauthpopup({
        path: site_url + 'gdrive',
        callback: function () {
            Buckty.handler('', window.location.href);
        }
    });
}

Buckty.RemoveGdrive = function () {
    Buckty.loading('s');
    $.post(site_url + '/gdrive/disconnect', {
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (d) {
        var d = JSON.parse(d);
        if (d.error_code === 1 && d.msg.auth === 0) {
            Buckty.AuthGdrive();
            return false;
        }
        if (d.error_code === 1) {
            Buckty.toast('exclamation-triangle', d.msg.message);
        } else if (d.error_code === 0) {
            Buckty.toast('exclamation-triangle', d.msg.message);
            Buckty.handler('check-cricle-o', window.location.href);
        }
    });
}

Buckty.AuthDropbox = function () {
    $.oauthpopup({
        path: site_url + 'dropbox',
        callback: function () {
            Buckty.handler('', window.location.href);
        }
    });
}

Buckty.RemoveDropbox = function () {
    Buckty.loading('s');
    $.post(site_url + '/dropbox/disconnect', {
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (d) {
        var d = JSON.parse(d);
        if (d.error_code === 1) {
            Buckty.toast('exclamation-triangle', d.msg.message);
        } else if (d.error_code === 0) {
            Buckty.toast('exclamation-triangle', d.msg.message);
            Buckty.handler('check-cricle-o', window.location.href);
        }
    });
}

Buckty.Recover = function () {
    $('.login_reg_block').toggle(), $('.recovery_block').toggle();
}

Buckty.loadRecover = function (view) {
    var view = view.data('w');
    var email = $('#emailR').val();
    var loader = $('.bt .loader');
    loader.show();
    if (email == '' || email == undefined) {
        Buckty.toastlog('Email is required!', 'error', 'log');
        return false;
    }
    switch (view) {
        case 'p':
            $.post(site_url + 'recover/password', {
                email: email,
                csrf_Buckty: Buckty.Buckty_getToken
            }).done(function (d) {
                var d = JSON.parse(d);
                if (d.code == 2) {
                    Buckty.toastlog(d.message, 'success', 'reset');
                } else if (d.code == 1) {
                    Buckty.toastlog(d.message, 'error', 'reset');
                }
                loader.hide();
            });
            break;
        case 'l':

            break;

        default:
            console.log('No Attempt');
    }
}


$(document.body).on('click', '.socialshare', function (e) {
    var provider = $(this).data('action');
    var hash = $(this).data('hash');
    var type = $(this).data('type');
    var link = site_url + 'shared/' + type + '/' + hash;

    switch (provider) {
        case 'facebook':
            $.oauthpopup({
                path: 'https://www.facebook.com/sharer/sharer.php?u=' + link,
                callback: function () {
                    return false;
                }
            });
            break;
        case 'google':
            $.oauthpopup({
                path: 'https://plus.google.com/share?url=' + link,
                callback: function () {
                    return false;
                }
            });
            break;
        case 'twitter':
            $.oauthpopup({
                path: 'https://twitter.com/home?status=' + link,
                callback: function () {
                    return false;
                }
            });
            break;
        case 'pinterest':
            $.oauthpopup({
                path: 'https://pinterest.com/pin/create/button/?url=' + link,
                callback: function () {
                    return false;
                }
            });
            break;
        case 'tumblr':
            $.oauthpopup({
                path: 'http://www.tumblr.com/share/link?url=' + link,
                callback: function () {
                    return false;
                }
            })
            break;
    }
    e.preventDefault();
});

Buckty.loadTree = function () {
    $.get(site_url + 'useraction/tree', {
        csrf_Buckty: Buckty.Buckty_getToken
    }).done(function (data) {
        var container = $('.folder_container ul');
        container.empty();
        container.append(data);
    });
}

Buckty.Reload = function () {
    Buckty.handler('', window.location.href);
}

Buckty.getDropboxList = function (path, parent) {
    if (path == undefined) {
        path = '/';
    }

    if (parent == undefined) {
        parent = 'n';
    }

    Buckty.loading('s');
    $.get(site_url + '/dropbox_list', {
        csrf_Buckty: Buckty.Buckty_getToken,
        path: path,
        parent: parent
    }).done(function (d) {
        var content = $(d).filter('#content').html();
        $('#content').html(content);
        Buckty.loading('h');
    })
}
Buckty.getDriveList = function (f) {
    if (f == undefined) {
        f = NULL;
    }

    Buckty.loading('s');
    $.get(site_url + '/drive_list', {csrf_Buckty: Buckty.Buckty_getToken, f: f}).done(function (d) {
        var content = $(d).filter('#content').html();
        $('#content').html(content);
        Buckty.loading('h');
    })
}

Buckty.getDropbox = function (file) {
    if (file == undefined) {
        return false;
    }
    Buckty.loading('s');
    $.post(site_url + 'dropbox/dbget', {csrf_Buckty: Buckty.Buckty_getToken, path: file}).done(function (r) {
        var r = JSON.parse(r);
        if (r.error_code === 1) {
            Buckty.toast('exclamation-triangle', r.msg.message);
        } else if (r.error_code === 0) {
            Buckty.toast('check-cricle-o', r.msg.message);
        }
        Buckty.loading('h');
        Buckty.RefreshSpace();
    });
}
Buckty.getDrive = function (file) {
    if (file == undefined) {
        return false;
    }
    Buckty.loading('s');
    $.post(site_url + 'gdrive/get', {csrf_Buckty: Buckty.Buckty_getToken, file: file}).done(function (r) {
        var r = JSON.parse(r);
        if (r.error_code === 1) {
            Buckty.toast('exclamation-triangle', r.msg.message);
        } else if (r.error_code === 0) {
            Buckty.toast('check-cricle-o', r.msg.message);
        }
        Buckty.loading('h');
        Buckty.RefreshSpace();
    });
}

Buckty.loadDisqus = function () {
    var shortname = site_info.disqus;
    var disqus_config = function () {
        this.page.url = ITEM_LINK; // Replace PAGE_URL with your page's canonical URL variable
        this.page.identifier = SHARED_IDENTITY; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    (function () {  // REQUIRED CONFIGURATION VARIABLE: EDIT THE SHORTNAME BELOW
        var d = document, s = d.createElement('script');

        s.src = '//' + shortname + '.disqus.com/embed.js';  // IMPORTANT: Replace EXAMPLE with your forum shortname!

        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
}
Buckty.toggleComments = function () {
    if ($('.side_bar').is(':visible')) {
        $('.side_bar').hide().animate({right: '-350px'});
    } else {
        $('.side_bar').show().animate({right: 0}, 400);
    }
}
Buckty.CurrentPage = function () {
    $('.side_menu ul li a').removeClass('selected');
    var pathname = (window.location.pathname.match(/[^\/]+$/));
    if (pathname !== null) {
        pathname = pathname['input'];
        var name = pathname.split('/');
        $(".side_menu ul li a").each(function () {
            if (window.location.href.indexOf($(this).attr("href")) > -1) {
                $(this).addClass("selected");
            }
        });
    }
}
Buckty.showMenu = function (e) {
    e.toggleClass('active');
    e.find('i').toggleClass('fa-navicon fa-close');
    $('.vert_block').toggleClass('open');
    $('.header_bar .logo_on_bar').toggleClass('open');
}

Buckty.Api = {
    UserApiAccess: function () {
        $.get(site_url + 'useraction/api', {csrf_Buckty: Buckty.Buckty_getToken, user: userData.id}).done(function (r) {
            $('body').append(r);
            Buckty.popup('js_ApiPopup', 'o');
        });
    },
    GenerateApi: function () {
        $.post(site_url + 'useraction/generateapi', {
            csrf_Buckty: Buckty.Buckty_getToken,
            user: userData.id
        }).done(function (r) {
            r = JSON.parse(r);
            if (r.error_code === 0) {
                $('#appendKey').html(r.msg.key);
                Buckty.toast('check-circle-o', r.msg.message);
            } else if (r.error_code === 1) {
                Buckty.toast('exclamation-triangle', r.msg.message);
            }
        })
    }
}