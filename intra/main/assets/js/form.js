(function ($, window, document, undefined) {
  var pluginName = "form";

  function Plugin(element, options) {
    defaults = {
      url: null,
      url_reject: null,
      wrapper: null,
      formTags: function () {
        return true;
      },
      onsubmit: function () {},
      beforeSubmit: function (self, el, data) {
        return true;
      },
      generateSuccess: function (data) {},
      onError: function (data) {},
      onSuccess: function (data) {},
      processBeforeSend: function (data) {
        return data;
      },
      formWrap: true,
      isReset: true,
      successMessage: "<strong>Sukses!</strong>",
      errorMessage: "<strong>Gagal!</strong> Terjadi kesalahan",
    };
    self = this;

    this.element = element;

    this.options = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;
    this.input = {};
    this.init();

    $(self.formTags).on("submit", function (e) {
      e.preventDefault();
      self.send($(this));
    });
  }

  Plugin.prototype = {
    init: function () {
      $(self.element).empty();
      self.generateFormTags(self.formTags);
      var key = 0;
      $.each(this.options.form, function (keys, value) {
        _count =
          typeof self.input.length == "undefined" ? 0 : self.input.length;

        if (value.readonly == true) {
          var input = self.generateReadonly(key, value, value.type);
          self.input[_count] = input;
        } else {
          switch (value.type) {
            case "text":
            case "password":
            case "hidden":
            case "npwp":
            case "number":
            case "currency":
            case "money":
            case "money_asing":
            case "decimal":
              var input = self.generateText(key, value, value.type);
              self.input[_count] = input;
              break;
            case "file":
              var input = self.generateFile(key, value);
              self.input[_count] = input;
              break;
            case "multiple_file":
              var input = self.generateMultipleFile(key, value);
              self.input[_count] = input;
              break;
            case "email":
              var input = self.generateEmail(key, value);
              self.input[_count] = input;
              break;
            case "textarea":
              var input = self.generateTextarea(key, value);
              self.input[_count] = input;
              break;
            case "tinymce":
              var input = self.generateTinymce(key, value);
              self.input[_count] = input;
              break;
            case "search":
              var input = self.generateSearch(key, value);
              self.input[_count] = input;
              break;
            case "dropdown":
              var input = self.generateDropdown(key, value);
              self.input[_count] = input;
              break;
            case "multiple":
              var input = self.generateMultiple(key, value);
              self.input[_count] = input;
              break;
            case "radio":
            case "radioList":
              var input = self.generateRadio(key, value);
              self.input[_count] = input;
              break;
            case "checkbox":
            case "checkboxList":
              var input = self.generateCheckbox(key, value);
              self.input[_count] = input;
              break;
            case "time":
              var input = self.generateTimePicker(key, value);
              self.input[_count] = input;
              break;
            case "date":
              var input = self.generateDate(key, value);
              self.input[_count] = input;
              break;
            case "dateTime":
              var input = self.generateDateTime(key, value);
              self.input[_count] = input;
              break;
            case "lifetimeDate":
              var input = self.generateLifetimeDate(key, value);
              self.input[_count] = input;
              break;
            case "date_range":
              var input = self.generateDateRange(key, value);
              self.input[_count] = input;
              break;
            case "date_range_lifetime":
              var input = self.generateDateRangeLifetime(key, value);
              self.input[_count] = input;
              break;
            case "dateperiod":
              var input = self.generateDatePeriod(key, value);
              self.input[_count] = input;
              break;
            case "matrix_resiko":
              var input = self.generateMatrixResiko(key, value);
              self.input[_count] = input;
              break;
            case "matrix_swakelola":
              var input = self.generateMatrixSwakelola(key, value);
              self.input[_count] = input;
              break;
            case "intro":
              var input = self.generateIntro(key, value);
              self.input[_count] = input;
              break;
            case "penilaianResiko":
              var input = self.generatePenilaianResiko(key, value);
              self.input[_count] = input;
              break;
            case "reviewResiko":
              var input = self.generateReviewResiko(key, value);
              self.input[_count] = input;
              break;
            case "fp3":
              var input = self.generateFP3(key, value);
              self.input[_count] = input;
              break;
          }
        }
        key++;
      });

      this.generateButton(this.formTags, this.options.button);

      $(".npwp", self.element).iMask({
        type: "fixed",
        mask: "99.999.999.9-999.999",
      });
      $(".decimal", self.element).iMask({
        type: "number",
      });

      $(".money", self.element).iMask({
        type: "number",
      });
    },

    /**
		Generate HTML table
		**/
    button: {
      buttonWrapper: function (element) {
        $(element).append('<div class="form-group btn-group"></div>');

        return $(".form-group.btn-group", element);
      },

      submit: function (element, key, value) {
        $(element).append(
          '<button type="submit" class="button is-primary btn-submit ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
      },
      reject: function (element, key, value) {
        $(element).append(
          '<a href="#" class="button is-danger reject-btn ' +
            value.class +
            '" >' +
            value.label +
            "</a>"
        );
        $(".reject-btn", element).on("click", function () {
          $(".form-keterangan-reject.modal-reject").addClass("active");
        });
      },
      delete: function (element, key, value) {
        $(element).append(
          '<button type="submit" class="button is-danger btn-submit ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
      },
      reset: function (element, key, value) {
        $(element).append(
          '<button type="button" class="button is-danger btn-reset ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
        $(".btn-reset", element).on("click", function (e) {
          var parent = $(this).closest(".form");
          $(".form-control", parent).val("");
        });
      },
      cancel: function (element, key, value) {
        $(element).append(
          '<button class="button btn-cancel ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
        $(".btn-cancel", element).on("click", function (e) {
          e.preventDefault();
          var _parent = $(this).parents(".modal");
          $(".close", _parent).trigger("click");
        });
      },
      yes: function (element, key, value) {
        $(element).append(
          '<button type="button" class="button is-success btn-yes ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
      },
      no: function (element, key, value) {
        $(element).append(
          '<button type="button" class="button is-danger btn-no ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
      },
      button: function (element, key, value) {
        $(element).append(
          '<button type="button" class="button is-primary ' +
            value.class +
            '" >' +
            value.label +
            "</button>"
        );
      },
      export: function (element, key, value) {
        // console.log(value);
        $(element).append(
          '<a href="' +
            value.link +
            '" class="button is-primary ' +
            value.class +
            '" >' +
            value.label +
            "</a>"
        );
      },
    },
    send: function (el) {
      self.removeError(el);
      if (self.options.beforeSubmit) {
        form = $(el);
        formData = new FormData($(el)[0]);
        formData = self.options.processBeforeSend(formData);
        try {
          if (typeof csrf_token_name !== "undefined") {
            var __csrfToken = null;
            if (
              typeof getCookie === "function" &&
              typeof csrf_cookie_name !== "undefined"
            ) {
              __csrfToken = getCookie(csrf_cookie_name);
            }
            if (!__csrfToken && typeof csrf_hash !== "undefined") {
              __csrfToken = csrf_hash;
            }
            if (__csrfToken) {
              if (typeof formData.has === "function") {
                if (!formData.has(csrf_token_name)) {
                  formData.append(csrf_token_name, __csrfToken);
                }
              } else {
                formData.append(csrf_token_name, __csrfToken);
              }
            }
          }
        } catch (e) {}
        $.ajax({
          async: false,
          url: $(form).attr("action"),
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          dataType: "json",
          beforeSend: function (xhr) {
            $(".btn-submit", form)
              .attr("disabled", "disabled")
              .addClass("btn-loader");
          },
          success: function (data, textStatus, jqXHR) {
            try {
              if (jqXHR && typeof jqXHR.getResponseHeader === "function") {
                var nextToken = jqXHR.getResponseHeader("X-CSRF-Token");
                if (nextToken && typeof csrf_hash !== "undefined") {
                  csrf_hash = nextToken;
                }
              }
            } catch (e) {}
            if (data.status == "success") {
              self.generateSuccess(data);
            } else {
              self.options.onError(data);
              self.generateError(data.form);
            }
          },
          error: function (xhr) {},
          complete: function (xhr) {
            form = $(".btn-submit", form);

            setTimeout(function () {
              form.attr("disabled", false);
              form.removeClass("btn-loader");
            }, 1000);
          },
        });
      }
    },
    generateButton: function (wrapper, button) {
      var _wrapper = this.button.buttonWrapper(wrapper);

      $.each(button, function (key, value) {
        switch (value.type) {
          case "submit":
            self.button.submit(_wrapper, key, value);
            break;
          case "reject":
            self.button.reject(_wrapper, key, value);
            break;
          case "yes":
            self.button.yes(_wrapper, key, value);
            break;
          case "no":
            self.button.no(_wrapper, key, value);
            break;
          case "reset":
            self.button.reset(_wrapper, key, value);
            break;
          case "delete":
            self.button.delete(_wrapper, key, value);
            break;
          case "cancel":
            self.button.cancel(_wrapper, key, value);
            break;
          case "export":
            self.button.export(_wrapper, key, value);
            break;
          default:
            self.button.button(_wrapper, key, value);
        }
      });
    },
    generateWrapper: function (
      key,
      element,
      read_only = false,
      is_half = false
    ) {
      var _read_only = read_only == false ? "" : "read_only";
      var _half = is_half == false ? "" : "half";

      $(element).append(
        '<fieldset class="form-group ' +
          _read_only +
          " " +
          _half +
          " form" +
          key +
          '" for=""></fieldset>'
      );
      return $("fieldset", element)[key];
    },
    generateFile: function (key, data, type) {
      _this = this;
      var _closeTags;
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      var wrapper = this.generateWrapper(
        key,
        this.formTags,
        data.read_only,
        data.half
      );
      is_required = /required/.test(data.rules) ? "*" : "";
      $(wrapper).append(
        '<label for="' + data.id + '">' + data.label + is_required + "</label>"
      );
      $(wrapper).append(
        '<input type="file" class="form-control" id="' +
          data.id +
          '"  name="' +
          data.field +
          '" class="' +
          data.class +
          '"/><input type="hidden" name="' +
          data.field +
          '" value="' +
          data.value +
          '">'
      );
      if (data.value != "") {
        $(wrapper).append(
          '<div class="fileUploadBlock"><i class="fa fa-upload"></i>&nbsp;<a href="' +
            data.upload_path +
            "/" +
            data.value +
            '" target="_blank">' +
            data.value +
            '</a><div class="deleteFile"><i class="fa fa-trash"></i></div></div>'
        );
        $(".deleteFile", wrapper).on("click", function (e) {
          $(".form-control", wrapper).show();
          $("input[type=hidden]", wrapper).val("");
          $(".fileUploadBlock", wrapper).remove();
        });
        $(".form-control", wrapper).hide();
        $(".error-help", wrapper).hide();
      }

      $("input.form-control ", wrapper).on("change", function (e) {
        _this.removeError(wrapper);

        var _files = $(this);
        var myFormData = new FormData();
        myFormData.append(
          data.field,
          $(".form-control", wrapper).prop("files")[0]
        );
        myFormData.append("allowed_types", data.allowed_types);

        $.ajax({
          url: data.upload_url,
          type: "POST",
          processData: false,
          contentType: false,
          dataType: "json",
          data: myFormData,
          beforeSend: function () {
            _files.addClass("sending");
          },
          success: function (xhr) {
            _files.val("");
            if (xhr.status == "success") {
              $(wrapper).append(
                '<div class="fileUploadBlock"><i class="fa fa-upload"></i>&nbsp;<a href="' +
                  xhr.upload_path +
                  '" target="blank">' +
                  xhr.file_name +
                  '</a><div class="deleteFile"><i class="fa fa-trash"></i></div></div>'
              );
              $(".deleteFile", wrapper).on("click", function (e) {
                $(".form-control", wrapper).show();
                $("input[type=hidden]", wrapper).val("");
                $(".fileUploadBlock", wrapper).remove();
              });
              $("input[type=hidden]", wrapper).val(xhr.file_name);
              $(".form-control", wrapper).hide();
              $(".error-help", wrapper).hide();
              _files.removeClass("sending");
            } else {
              $(wrapper).addClass("form-error");
              $(wrapper).append(
                '<small class="error-help">' + xhr.message + "</small>"
              );
              _files.removeClass("sending");
            }
          },
        });
      });
      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateMultipleFile: function (key, data, type) {
      _this = this;
      var _closeTags;
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      var wrapper = this.generateWrapper(
        key,
        this.formTags,
        data.read_only,
        data.half
      );
      is_required = /required/.test(data.rules) ? "*" : "";
      $(wrapper).append(
        '<label for="' + data.id + '">' + data.label + is_required + "</label>"
      );
      var no = 1;
      var input = $(
        '<input type="file" class="form-control " id="' +
          data.id +
          '" data-no="' +
          no +
          '" name="' +
          data.field +
          "[" +
          no +
          ']" class="' +
          data.class +
          '"/><input type="hidden" name="' +
          data.field +
          "[" +
          no +
          ']" value="' +
          data.value +
          '">'
      );
      $(wrapper).append(input);
      if (data.value != "") {
        $(wrapper).append(
          '<div class="fileUploadBlock"><i class="fa fa-upload"></i>&nbsp;<a href="' +
            data.upload_path +
            "/" +
            data.value +
            '" target="_blank">' +
            data.value +
            '</a><div class="deleteFile"><i class="fa fa-trash"></i></div></div>'
        );
        $(".deleteFile", wrapper).on("click", function (e) {
          $(".form-control", wrapper).show();
          $("input[type=hidden]", wrapper).val("");
          $(".fileUploadBlock", wrapper).remove();
        });
        $(".form-control", wrapper).hide();
        $(".error-help", wrapper).hide();
      }
      $(wrapper).append(
        '<a class="tambahLampiran" href="#"> + Tambah Lampiran</a>'
      );

      function process(obj) {
        _this.removeError(wrapper);
        no = obj.data("id");
        var _files = obj;
        var myFormData = new FormData();
        myFormData.append(data.field + "[" + no + "]", $(obj).prop("files")[0]);
        myFormData.append("allowed_types", data.allowed_types);

        $.ajax({
          url: data.upload_url,
          type: "POST",
          processData: false,
          contentType: false,
          dataType: "json",
          data: myFormData,
          beforeSend: function () {
            _files.addClass("sending");
          },
          success: function (xhr) {
            _files.val("");
            if (xhr.status == "success") {
              $(_files).after(
                '<div class="fileUploadBlock" id="[name="' +
                  data.field +
                  "[" +
                  no +
                  ']"]"><i class="fa fa-upload"></i>&nbsp;<a href="' +
                  xhr.upload_path +
                  '" target="blank">' +
                  xhr.file_name +
                  '</a><div class="deleteFile" ><i class="fa fa-trash"></i></div></div>'
              );
              $(".deleteFile", wrapper).on("click", function (e) {
                $(
                  '.form-control[name="' + data.field + "[" + no + ']"]',
                  wrapper
                ).show();
                $(
                  'input[type=hidden][name="' + data.field + "[" + no + ']"]',
                  wrapper
                ).val("");
                $(".fileUploadBlock", wrapper).remove();
              });
              $(
                'input[type=hidden][name="' + data.field + "[" + no + ']"]',
                wrapper
              ).val(xhr.file_name);
              $(_files).hide();
              $(".error-help", wrapper).hide();
              _files.removeClass("sending");
            } else {
              $(wrapper).addClass("form-error");
              $(wrapper).append(
                '<small class="error-help">' + xhr.message + "</small>"
              );
              _files.removeClass("sending");
            }
          },
        });
      }
      $(".tambahLampiran", wrapper).on("click", function (e) {
        e.preventDefault();
        _input = input.clone();
        no = parseInt(_input.data("no")) + 1;
        console.log(_input);
        $(_input).attr("name", data.field + "[" + no + "]");

        $(this).before(_input);
        // $(this).before('<div class="fileUploadBlock"><i class="fa fa-upload"></i>&nbsp;<a href="'+data.upload_path+'/'+data.value+'" target="_blank">'+data.value+'</a><div class="deleteFile"><i class="fa fa-trash"></i></div></div>');
        $('input[type="file"]', _input).show();

        _input.on("change", function (e) {
          process($(this));
        });
      });
      $("input.form-control ", wrapper).on("change", function (e) {
        process($(this));
      });

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateText: function (key, data, type) {
      var _closeTags;

      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;

      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      _class = type == "npwp" ? "npwp" : "";
      _class = type == "money" || type == "money_asing" ? "money" : "";
      is_required = /required/.test(data.rules) ? "*" : "";
      is_half = /half/.test(data.class) ? "half" : "";

      if (type != "hidden") {
        var wrapper = this.generateWrapper(
          key,
          this.formTags,
          data.read_only,
          data.half
        );
        if (typeof data.label != "undefined") {
          $(wrapper).append(
            '<label for="' +
              data.id +
              '">' +
              data.label +
              is_required +
              "</label>"
          );
        }
        if (typeof data.icon != "undefined") {
          $(wrapper).append(
            '<div class="input-group"><div class="input-group-addon" ><i class="' +
              data.icon +
              '"></i></div><input type="' +
              type +
              '" class="form-control ' +
              data.class +
              '" id="' +
              data.id +
              '" value="' +
              data.value +
              '" name="' +
              data.field +
              '" placeholder="' +
              data.placeholder +
              '"/></div>'
          );
        } else {
          if (type == "npwp") {
            data.class += " npwp";
            type = "text";
          }
          if (type == "decimal") {
            data.class += " decimal";
            type = "text";
          }
          if (type == "currency") {
            type = "text";
            // data.value = $.number(data.value,0, '.');
            data.class += " money";
          }
          if (type == "email") {
            // data.class += ' decimal';
            type = "email";
          }
          if (type == "money") {
            type = "text";
            data.value = numeral(data.value).format("0,0.00");
            // console.log(wrapper);
            $(wrapper).append("Rp ");
            data.class += " money";
            if (data.value == "") {
              data.value = "0.00";
            }
          }
          if (type == "money_asing") {
            var _select =
              "<select name='" +
              data.field[0] +
              "' class='form-control mg-xs-2' >";
            $.each(data.source, function (keys, value) {
              _selected = data.value[0] == keys ? "selected" : "";
              _select +=
                "<option value='" +
                keys +
                "' " +
                _selected +
                ">" +
                value +
                "</option>";
            });
            _select += "</select>";
            $(wrapper).append(_select);
            _class += " mg-xs-10";
            data.field = data.field[1];
            data.value = $.number(data.value[1], 0, ".");
            if (data.value == "") {
              data.value = "0.00";
            }
          }

          $(wrapper).append(
            '<input type="' +
              type +
              '" class="form-control ' +
              _class +
              " " +
              data.class +
              '" id="' +
              data.id +
              '" value="' +
              data.value +
              '" name="' +
              data.field +
              '" placeholder="' +
              data.placeholder +
              '"/></div>'
          );
        }
      } else {
        $(this.formTags).append(
          '<input type="' +
            type +
            '" class="form-control ' +
            data.class +
            ' " id="' +
            data.id +
            '" value="' +
            data.value +
            '" name="' +
            data.field +
            '"/>'
        );
      }

      if (type == "money") {
        var range = $('.form-control[name="' + data.field + '"]', wrapper);
        var _l = range.val().length;

        var pos = _l - 3;
        setCaretToPos(range[0], pos);
        range.on("click mousedown", function () {
          setCaretToPos(range[0], pos);
        });
      }

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateDate: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(
        '<label for="' +
          data.id +
          '">' +
          data.label +
          is_required +
          '</label><div class="wrapper"></div>'
      );

      $(".wrapper", wrapper).append(
        '<input type="text" class="form-control datePicker ' +
          data.class +
          '" id="' +
          data.id +
          key +
          '" value="' +
          data.value +
          '" name="' +
          data.field +
          '" />'
      );

      $(".datePicker", wrapper).datetimepicker({
        timepicker: false,
        format: "Y-m-d",
        scrollMonth: false,
        scrollInput: false,
      });

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateEmail: function (key, data, type) {
      var wrapper = this.generateWrapper(key, this.formTags);
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";

      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }

      $(wrapper).append(
        '<input type="email" class="form-control ' +
          data.class +
          '" id="' +
          data.id +
          '" name="' +
          data.field +
          '" >'
      );

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateDateTime: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(
        '<label for="' +
          data.id +
          '">' +
          data.label +
          is_required +
          '</label><div class="wrapper"></div>'
      );

      $(".wrapper", wrapper).append(
        '<input type="text" class="form-control datePicker ' +
          data.class +
          '" id="' +
          data.id +
          key +
          '" value="' +
          data.value +
          '" name="' +
          data.field +
          '" />'
      );

      $(".datePicker", wrapper).datetimepicker({
        format: "Y-m-d H:i",
        scrollMonth: false,
        scrollInput: false,
      });
      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateLifetimeDate: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(
        '<label for="' +
          data.id +
          '">' +
          data.label +
          is_required +
          '</label><div class="wrapper"></div>'
      );
      var setValue;
      if (data.value == "lifetime") {
        setValue = "";
      } else {
        setValue = data.value;
      }
      $(".wrapper", wrapper).append(
        '<input type="text" class="form-control datePicker ' +
          data.class +
          '" id="' +
          data.id +
          key +
          '" value="' +
          setValue +
          '" name="' +
          data.field +
          '" />'
      );
      $(".wrapper", wrapper).append(
        '<div class="lifetimeWrapper"><input type="checkbox" class="form-control lifetime" id="' +
          data.id +
          key +
          '" value="lifetime" name="' +
          data.field +
          '" />&nbsp;<span>Seumur Hidup</span></div>'
      );
      if (data.value == "lifetime") {
        $(".lifetime.form-control", wrapper).prop("checked", "checked");
      }
      $(".datePicker", wrapper).datetimepicker({
        timepicker: false,
        format: "Y-m-d",
        scrollMonth: false,
        scrollInput: false,
      });

      if ($(".lifetime.form-control", wrapper).is(":checked")) {
        $(".form-control.datePicker", wrapper).hide();
      }

      $(".lifetime.form-control", wrapper).on("change", function () {
        $(".form-control.datePicker", wrapper).toggle();
        $(".form-control.datePicker", wrapper).val("");
      });

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateDateRangeLifetime: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(
        '<label for="' +
          data.id +
          '">' +
          data.label +
          is_required +
          '</label><div class="rangeWrapper"></div><div class="wrapper"></div>'
      );
      $.each(data.field, function (key, value) {
        if (key != 0) {
          $(".rangeWrapper", wrapper).append(" - ");
        }
        $(".rangeWrapper", wrapper).append(
          '<input type="text" class="form-control datePicker dateRange ' +
            data.class +
            '" id="' +
            data.id +
            key +
            '" value="' +
            data.value +
            '" name="' +
            value +
            '" />'
        );
      });
      $(".wrapper", wrapper).append(
        '<div class="lifetimeWrapper"><input type="checkbox" class="form-control lifetime ' +
          data.class +
          '" id="' +
          data.id +
          key +
          '" value="lifetime" name="' +
          data.field +
          '" />&nbsp;<span>Seumur Hidup</span></div>'
      );

      if (data.value == "lifetime") {
        $(".lifetime.form-control", wrapper).prop("checked", "checked");
      }

      $(".datePicker", wrapper).datetimepicker({
        timepicker: false,
        format: "Y-m-d",
        scrollMonth: false,
        scrollInput: false,
      });

      if ($(".lifetime.form-control", wrapper).is(":checked")) {
        $(".rangeWrapper", wrapper).hide();
      }

      $(".lifetime.form-control", wrapper).on("change", function () {
        $(".rangeWrapper", wrapper).toggle();
        $(".form-control.datePicker.dateRange", wrapper).val("");
      });

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateReadonly: function (key, data, type) {
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      var wrapper = this.generateWrapper(key, this.formTags, true);
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' + data.id + '">' + data.label + "</label>"
        );
      }
      if (type == "file") {
        $(wrapper).append(
          '<b>:</b><span><a href="' +
            base_url +
            "assets/lampiran/" +
            data.field +
            "/" +
            data.value +
            '" target="blank">' +
            data.value +
            "</a></span>"
        );
      } else if (type == "radio") {
        data.value =
          typeof data.source[data.value] == "undefined" ||
          data.source[data.value] == null
            ? ""
            : data.source[data.value];
        $(wrapper).append("<b>:</b><span>" + data.value + "</span>");
      } else if (type == "dropdown") {
        data.value =
          typeof data.source[data.value] == "undefined" ||
          data.source[data.value] == null
            ? ""
            : data.source[data.value];
        $(wrapper).append("<b>:</b><span>" + data.value + "</span>");
      } else if (type == "hidden") {
        $(wrapper).append(
          '<div style="display: none"><b>:</b><span>' +
            data.value +
            "</span></div>"
        );
      } else if (type == "multiple") {
        var _val = data.value.split(",");
        var _return = "";

        $.each(_val, function (key, value) {
          _return += data.source[value] + ", ";
        });
        $(wrapper).append("<b>:</b><span>" + _return + "</span>");
      } else if (type == "date") {
        $(wrapper).append(
          "<b>:</b><span>" + defaultDate(data.value) + "</span>"
        );
      } else if (type == "dateTime") {
        $(wrapper).append(
          "<b>:</b><span>" + defaultDateTime(data.value) + "</span>"
        );
      } else if (type == "date_range") {
        data.value[0] =
          typeof data.value[0] == "undefined" || data.value[0] == null
            ? ""
            : data.value[0];
        data.value[1] =
          typeof data.value[1] == "undefined" || data.value[1] == null
            ? ""
            : data.value[1];
        $(wrapper).append(
          "<b>:</b><span>" +
            defaultDate(data.value[0]) +
            " sampai " +
            defaultDate(data.value[1]) +
            "</span>"
        );
      } else if (type == "currency") {
        $(wrapper).append("<b>:</b><span>" + data.value + "</i></span>");
      } else if (type == "money") {
        $(wrapper).append(
          "<b>:</b><span>Rp. " +
            $.number(data.value, 0, ".", ",") +
            "</i></span>"
        );
      } else if (type == "money_asing") {
        var __currency;
        __currency =
          typeof data.source[data.value[0]] == "undefined"
            ? ""
            : data.source[data.value[0]];
        $(wrapper).append(
          "<b>:</b><span>" + __currency + " " + data.value[1] + "</i></span>"
        );
      } else {
        $(wrapper).append("<b>:</b><span>" + data.value + "</span>");
      }

      return $(".form-control", wrapper);
    },
    generateTextarea: function (key, data, type) {
      var wrapper = this.generateWrapper(key, this.formTags);
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";

      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }

      $(wrapper).append(
        '<textarea class="form-control ' +
          data.class +
          '" id="' +
          data.id +
          '" name="' +
          data.field +
          '" >' +
          data.value +
          "</textarea>"
      );

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateTinymce: function (key, data, type) {
      var wrapper = this.generateWrapper(key, this.formTags);
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";

      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }

      $(wrapper).append(
        '<textarea class="form-control tinymce ' +
          data.class +
          '" id="' +
          data.id +
          '" name="' +
          data.field +
          '" >' +
          data.value +
          "</textarea>"
      );

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateDropdown: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      var wrapper = this.generateWrapper(key, this.formTags);
      is_required = /required/.test(data.rules) ? "*" : "";
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }

      var _select =
        "<select name='" +
        data.field +
        "' id='" +
        data.id +
        "' class='form-control " +
        data.class +
        "' >";
      $.each(data.source, function (keys, value) {
        _selected = data.value == keys ? "selected" : "";
        _select +=
          "<option value='" +
          keys +
          "' " +
          _selected +
          ">" +
          value +
          "</option>";
      });
      _select += "</select>";

      $(wrapper).append(_select);

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateMultiple: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      var wrapper = this.generateWrapper(key, this.formTags);
      is_required = /required/.test(data.rules) ? "*" : "";
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }

      var _select =
        "<select name='" +
        data.field +
        "[]' id='" +
        data.id +
        "' class='form-control " +
        data.class +
        " formMultiple' multiple>";
      var opt = data.value.split(",");
      $.each(data.source, function (keys, value) {
        _selected = $.inArray(keys, opt) >= 0 || keys == opt ? "selected" : "";
        _select +=
          "<option value='" +
          keys +
          "' " +
          _selected +
          ">" +
          value +
          "</option>";
      });
      _select += "</select>";

      $(wrapper).append(_select);

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateRadio: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      var wrapper = this.generateWrapper(key, this.formTags);
      is_required = /required/.test(data.rules) ? "*" : "";
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }
      var _check = '<div class="radioWrapper">';
      $.each(data.source, function (keys, value) {
        _checked = data.value === keys ? "checked" : "";

        if (data.type == "radioList") _check += '<div class="radioList">';
        _check +=
          "<input type='radio' value='" +
          keys +
          "' " +
          _checked +
          " name='" +
          data.field +
          "' class='form-control " +
          data.class +
          "'><label>" +
          value +
          "</label> ";
        if (data.type == "radioList") _check += "</div>";
      });
      _check += "</div>";
      $(wrapper).append(_check);

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateCheckbox: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      var wrapper = this.generateWrapper(key, this.formTags);
      is_required = /required/.test(data.rules) ? "*" : "";
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }
      var _check = '<div class="checkboxWrapper">';

      if (data.type == "checkboxList") {
        var arr = data.value.split(",");
      }
      $.each(data.source, function (keys, value) {
        _checked = data.value === keys ? "checked" : "";

        if (data.type == "checkboxList") {
          // _check +='<div class="checkboxList">';
          _checked = $.inArray(keys, arr) >= 0 ? "checked" : "";
        }

        _check +=
          "<div class='checkboxList'><input type='checkbox' value='" +
          keys +
          "' " +
          _checked +
          " name='" +
          data.field +
          "[]' class='form-control " +
          data.class +
          "'><label>" +
          value +
          "</label> </div>";
        // if(data.type=='checkboxList') _check +='</div>';
      });
      _check += "</div>";
      $(wrapper).append(_check);

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateTimePicker: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      var wrapper = this.generateWrapper(key, this.formTags);
      is_required = /required/.test(data.rules) ? "*" : "";

      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }
      $(wrapper).append(
        '<div class="input-group timepicker"><input type="text" class="form-control ' +
          data.class +
          '" id="' +
          data.id +
          '" value="' +
          data.value +
          '" name="' +
          data.field +
          '" placeholder="' +
          data.placeholder +
          '"/></div>'
      );

      $(".timepicker", wrapper).clockpicker({ autoclose: true });

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateSearch: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      var wrapper = this.generateWrapper(key, this.formTags);
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }
      $(wrapper).append(
        '<input type="text" class="form-control searchInput ' +
          data.class +
          '" autocomplete="off" id="' +
          data.id +
          '" value="' +
          data.value +
          '" name="' +
          data.field +
          '" placeholder="' +
          data.placeholder +
          '"/><div class="searchOption"></div>'
      );

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },

    generateDateRange: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;

      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(
        '<label for="' +
          data.id +
          '">' +
          data.label +
          is_required +
          '</label><div class="rangeWrapper"></div>'
      );

      $.each(data.field, function (key, value) {
        var __val = "";
        if (data.value[key] != "" && data.value[key] != null) {
          __val = data.value[key];
        }

        if (key != 0) {
          $(".rangeWrapper", wrapper).append(" - ");
        }
        $(".rangeWrapper", wrapper).append(
          '<input type="text" class="form-control datePicker dateRange ' +
            data.class +
            '" id="' +
            data.id +
            key +
            '" value="' +
            __val +
            '" name="' +
            value +
            '" />'
        );
      });

      $(".datePicker", wrapper).datetimepicker({
        timepicker: false,
        format: "Y-m-d",
        scrollMonth: false,
        scrollInput: false,
      });

      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateDatePeriod: function (key, data) {
      data.id = typeof data.id == "undefined" ? "" : "input" + key;
      data.value =
        typeof data.value == "undefined" || data.value == null
          ? ""
          : data.value;
      data.placeholder =
        typeof data.placeholder == "undefined" ? "" : data.placeholder;
      data.class =
        typeof data.class == "undefined" || data.class == null
          ? ""
          : data.class;
      data.rules = typeof data.rules == "undefined" ? "" : data.rules;
      is_required = /required/.test(data.rules) ? "*" : "";
      var wrapper = this.generateWrapper(key, this.formTags);
      if (typeof data.label != "undefined") {
        $(wrapper).append(
          '<label for="' +
            data.id +
            '">' +
            data.label +
            is_required +
            "</label>"
        );
      }
      $(wrapper).append(
        '<input type="text" class="form-control dateperiod ' +
          data.class +
          '" id="' +
          data.id +
          '" value="' +
          data.value +
          '" name="' +
          data.field +
          '" />'
      );
      $(".dateperiod", wrapper).daterangepicker({
        datepickerOptions: {
          maxDate: null,
        },
      });
      if (data.caption) {
        $(wrapper).append(
          '<span class="form-caption">' + data.caption + "</span>"
        );
      }
      return $(".form-control", wrapper);
    },
    generateFormTags: function () {
      var url =
        this.options.url == "" ||
        this.options.url == null ||
        typeof this.options.url == "undefined"
          ? ""
          : 'action="' + this.options.url + '"';
      var url_reject =
        this.options.reject == "" ||
        this.options.reject == null ||
        typeof this.options.reject == "undefined"
          ? ""
          : 'action="' + this.options.reject + '"';
      if (this.options.formWrap) {
        $(this.element).append(
          '<div class="form blockWrapper"><form ' +
            url +
            ' method="POST" enctype="multitype/form-data"></form></div>'
        );
        $(".form-keterangan-reject .generate-content").append(
          "<form " +
            url_reject +
            ' method="POST" enctype="multitype/form-data"><div class="fkr-content"><fieldset class="form-group" for="" style="display: block;"><label for="keterangan">Keterangan</label><textarea type="text" class="form-control fkr-textarea" id="" value="" name="keterangan" placeholder="isi keterangan penolakan"></textarea></fieldset></div><div class="fkr-btn-group"><button class="is-danger" type="submit" name="reject">Reject</button></div></form>'
        );
        this.formTags = $("form", this.element);
      } else {
        $(this.element).append('<div class="form blockWrapper"></div>');
        $(".form-keterangan-reject").append(
          '<div class="form blockWrapper"></div>'
        );
        this.formTags = $(".form", this.element);
      }
    },

    generatePenilaianResiko: function (key, data) {
      var matrix =
        '<div class="ps-wrapper" style="width: 100%;"> <table class="penilaian_resiko preview"> <tr class="header"> <th rowspan="2">No</th> <th rowspan="2">Daerah Risiko</th> <th rowspan="2">Apa</th> <th colspan="5" style="text-align: center;">Konsekuensi <br> L/M/H</th> </tr> <tr class="header bottom"> <th>Manusia</th> <th>Aset</th> <th>Lingkungan</th> <th>Reputasi <br>& Hukum</th> <th>Catatan</th> </tr> <tr class="q1"> <td>1.</td> <td>Jenis Pekerjaan</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q2"> <td>2.</td> <td>Lokasi Kerja</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q3"> <td>3.</td> <td>Materi Peralatan yang digunakan.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q4"> <td>4.</td> <td>Potensi paparan terhadap bahaya tempat kerja.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q5"> <td>5.</td> <td>Potensi paparan terhadap bahaya bagi personil.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q6"> <td>6.</td> <td>Pekerjaan secara bersamaan oleh kontraktor berbeda.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q7"> <td>7.</td> <td>Jangka Waktu Pekerjaan.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q8"> <td>8.</td> <td>Konsekuensi pekerjaan potensian.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q9"> <td>9.</td> <td>Pengalaman Kontraktor.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr class="q10"> <td>10.</td> <td>Paparan terhadap publisitas negatif.</td> <td><input type="text" placeholder="isi" class="input" name="apa[]"></td> <td><input name="manusia[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="asset[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="lingkungan[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><input name="hukum[]" type="number" placeholder="0" class="input nm-tg" required></td> <td><span id="catatan" class="catatan">?</span></td> </tr> <tr> <td colspan="7" style="text-align:right;">Total</td> <td id="total"><span class="catatan">?</span></td> </tr> </table></div>';
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(matrix);
      // console.log(wrapper);
      return $(".form-control", wrapper);
    },

    generateMatrixResiko: function (key, data) {
      var matrix =
        '<div class="matrix-wrapper"><table class="matrix"><tr><td rowspan="8" class="title rotated" style="vertical-align: middle;">Probability</td></tr><tr><td><div class="matrix-rate">5. Almost Certain</div></td><td><div class="matrix-box yellow m5">5</div></td><td><div class="matrix-box orange m10">10</div></td><td><div class="matrix-box red m15">15</div></td><td><div class="matrix-box red m20">20</div></td><td><div class="matrix-box red m25">25</div></td></tr><tr><td><div class="matrix-rate">4. Likely</div></td><td><div class="matrix-box green-light m4">4</div></td><td><div class="matrix-box yellow m8">8</div></td><td><div class="matrix-box orange m12">12</div></td><td><div class="matrix-box red m16">16</div></td><td><div class="matrix-box red m20">20</div></td></tr><tr><td><div class="matrix-rate">3. Moderate</div></td><td><div class="matrix-box green m3">3</div></td><td><div class="matrix-box yellow m6">6</div></td><td><div class="matrix-box yellow m9">9</div></td><td> <div class="matrix-box orange m12">12</div></td><td><div class="matrix-box red m15">15</div></td></tr><tr><td><div class="matrix-rate">2. Unlikely</div></td><td><div class="matrix-box green m2">2</div></td><td><div class="matrix-box green-light m4">4</div></td><td><div class="matrix-box yellow m6">6</div></td><td><div class="matrix-box yellow m8">8</div></td><td><div class="matrix-box orange m10">10</div></td></tr><tr><td><div class="matrix-rate">1. Rare</div></td><td><div class="matrix-box green m1">1</div></td><td><div class="matrix-box green m2">2</div></td><td><div class="matrix-box green m3">3</div></td><td><div class="matrix-box green-light m4">4</div></td><td><div class="matrix-box yellow m5">5</div></td></tr><tr><td></td><td><div class="matrix-rate">1. Insignificant</div></td><td><div class="matrix-rate">2. Minor</div></td><td><div class="matrix-rate">3. Moderate</div></td><td><div class="matrix-rate">4. Significant</div></td><td><div class="matrix-rate">5. Catastrophic</div></td></tr><tr><td colspan="6" class="title">IMPACT</td></tr></table><div class="matrix-info"><div class="info-item"><span class="green">1 - 4</span>Low</div><div class="info-item"><span class="yellow">5 - 9</span>Moderate</div><div class="info-item"><span class="orange">10 - 12</span>High</div><div class="info-item"><span class="red">15 - 25</span>Extreme</div></div></div><div class="alert"></div>';
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(matrix);
      // console.log(wrapper);
      return $(".form-control", wrapper);
    },
    generateMatrixSwakelola: function (key, data) {
      var matrix =
        '<div class="matrix-swakelola-wrapper"><div class="matrix-swakelola"><div class="ms-item green m1">1</div><div class="ms-item green m2">2</div><div class="ms-item green m3">3</div><div class="ms-item green m4">4</div><div class="ms-item green m5">5</div><div class="ms-item green-light m6">6</div><div class="ms-item green-light m7">7</div><div class="ms-item green-light m8">8</div><div class="ms-item green-light m9">9</div><div class="ms-item green-light m10">10</div><div class="ms-item green-light sw m11">11</div><span class="ms-line"></span><div class="ms-item yellow pk m12">12</div><div class="ms-item yellow m13">13</div><div class="ms-item red m14">14</div><div class="ms-item red m15">15</div></div></div>';
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(matrix);
      // console.log(wrapper);
      return $(".form-control", wrapper);
    },
    generateIntro: function (key, data) {
      var matrix =
        '<div class="intro-wrapper"><div class="intro"><div class="intro-icon"></div><div class="intro-title">FPPBJ</div><div class="intro-caption">FORMULIR PERMOHONAN PENGADAAN BARANG/JASA (FPPBJ). <br>Form ini untuk membuat perencanaan pengadaan baru dengan metode : Pelelangan, Pemilihan Langsung, Pengadaan Langsung, Penunjukan Langsung, Swakelola</div></div></div>';
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(matrix);
      // console.log(wrapper);
      return $(".form-control", wrapper);
    },
    generateFP3: function (key, data) {
      var matrix =
        '<div id="regForm"><!-- <form id="regForm"> --><div class="tab" id="tab-intro"><div class="tab-content"><div class="intro-wrapper"><div class="intro"><div class="intro-icon"><img src="' +
        base_url +
        '/assets/images/edit-icon.png" alt="" style="height: 175px"></div><div class="intro-caption">Buat FP3 untuk merubah nama pengadaan, metode pengadaan dan jadwal pengadaan.</div><div class="intro-title"><button type="button" id="btnUbah">Ubah</button></div></div></div><div class="intro-wrapper"><div class="intro"><div class="intro-icon"><img src="' +
        base_url +
        '/assets/images/delete-icon.png" alt="" style="height: 175px"></div><div class="intro-caption">Buat FP3 Untuk Membatalkan FPPBJ.</div><div class="intro-title"><button type="button" id="btnBatal">Batal</button></div></div></div></div><div class="tab-footer"></div></div><div class="tab" id="formUbah"><div class="tab-form-header active">Ubah</div><div class="tab-form-header" id="switchBatal">Batal</div><div class="tab-content">&nbsp;</div><div class="tab-footer">&nbsp;</div></div><div class="tab" id="formBatal"><div class="tab-form-header" id="switchUbah">Ubah</div><div class="tab-form-header active">Batal</div><div class="tab-content">Buat FP3 Untuk Membatalkan FPPBJ?</div><div class="tab-footer">&nbsp;</div></div><!-- </form> --></div>';
      var wrapper = this.generateWrapper(key, this.formTags);

      $(wrapper).append(matrix);
      // console.log(wrapper);
      return $(".form-control", matrix);
    },

    generateSuccess: function (xhr) {
      this.options.onSuccess(xhr);
      this.generateAlert("success");

      if (this.options.isReset) {
        this.resetForm();
      }
      this.options.generateSuccess();
    },

    resetForm: function () {
      $(".form-control", this.element).val("");
      $(".form-control", this.element).show();
      $("input[type=hidden]", this.element).val("");
      $(".fileUploadBlock", this.element).remove();
    },

    generateAlert: function (type) {
      $(".alert-notif").remove();
      switch (type) {
        case "success":
          $(this.formTags).before(
            '<div class="alert alert-success alert-notif">' +
              this.options.successMessage +
              "</div>"
          );
          break;
        case "success":
          $(this.formTags).before(
            '<div class="alert alert-success alert-notif">' +
              this.options.successMessage +
              "</div>"
          );
          break;
        case "error":
          $(this.formTags).before(
            '<div class="alert alert-danger alert-notif">' +
              this.options.errorMessage +
              "</div>"
          );
          break;
      }
      setTimeout(function () {
        $(".alert-notif", this.formTags).fadeOut();
      }, 3000);
    },

    generateError: function (data) {
      this.generateAlert("error");
      _this = this;
      $.each(data, function (key, value) {
        if (value != "" && value != null && typeof value != "undefined") {
          el = $('[name*="' + key + '"]', _this.element);

          el.addClass("field-error");
          wrapper = el.closest(".form-group");
          wrapper.addClass("form-error");
          wrapper.append('<small class="error-help">' + value + "</small>");
        }
      });
    },
    removeError: function (el) {
      $(".field-error", el).removeClass("field-error");
      $(".form-error").removeClass("form-error");
      $(".error-help", el).remove();
    },
    destroy: function (el) {
      $(this).empty();
    },
  };

  $.fn[pluginName] = function (options) {
    this.each(function () {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, pluginName, new Plugin(this, options));
      }
    });
    // chain jQuery functions

    return this;
  };
})(jQuery, window, document);
