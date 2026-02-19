(function ($, window, document, undefined) {
  var pluginName = "modal";

  function Plugin(element, options) {
    defaults = {
      content: function (data) {
        return data;
      },
      open: function () {
        return;
      },
      url: "",
      method: "POST",
      header: "",
      data: {},
      dataSend: null,
      dataType: "json",

      render: function (el, data) {
        return data;
      },
      finish: function (el, data) {
        return data;
      },
      destroy: function (el, data) {
        return;
      },
    };
    var self = this;

    this.element = element;

    this.options = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;

    this.init();

    $(this.element).on("click", function (e) {
      e.preventDefault();
      $(this).data(pluginName).open();
    });

    $(".close", this.options.wrapper).on("click", function () {
      self.close();
    });

    $(this.options.wrapper).on("click", function (e) {
      self.close();
    });

    $(".modalWrapper", this.options.wrapper).on("click", function (e) {
      e.stopPropagation();
    });
  }

  Plugin.prototype = {
    init: function () {
      this.destroy(this.element);
      this.generateWrapper(this.element);
      this.generateBlocker();
      if (this.options.url == "") {
        this.options.url = $(this.element).attr("href");
      }
    },
    generateWrapper: function (el) {
      if (
        this.options.header == "" ||
        typeof this.options.header == "undefined"
      ) {
        // console.log(this);
        this.options.header = $(this.element).data("header");
      }

      $(this.element).attr("target", "modalInit");

      if ($("#modalInit").length == 0) {
        $("body").append(
          '<div class="modal" id="modalInit"><div class="modalWrapper" id="modalWrap"><div class="modal-dialog"><div class="modal-header"><button type="button" class="close"><span aria-hidden="true">×</span></button><h4 class="modal-title">' +
            this.options.header +
            '</h4></div><div class="modal-content"></div></div></div></div>'
        );
        $("body").append(
          '<div class="form-keterangan-reject modal-reject"><span class="fkr-btn-close close-modal-reject"><i class="fas fa-times"></i></span><div class="generate-content"></div></div>'
        );
        this.options.wrapper = $("#modalInit");
        return;
      }
      this.options.wrapper = $("#modalInit");
    },

    generateBlocker: function () {
      if ($("#modalBlocker").length == 0) {
        $("body").append('<div id="modalBlocker" class="modalBlocker"></div>');
        this.options.blocker = $("#modalBlocker");
        return;
      }
      this.options.blocker = $("#modalBlocker");
    },

    open: function (el) {
      var wrapper = $(".modal-content", this.options.wrapper);

      if (typeof this.options.open(el) == "undefined") {
        $("body").addClass("modal-open");

        $(this.options.wrapper).addClass("open");
        $(this.options.blocker).addClass("open");
        _fetch = this.fetch(this);

        this.options.data = _fetch;
        this.render(_fetch);
      }

      this.finish(wrapper);
    },
    fetch: function (callback) {
      if (typeof this.options.content() == "undefined") {
        var _ajax = null;

        var method = (this.options.method || "GET").toString().toUpperCase();
        var dataSend = this.options.dataSend;
        if (method === "POST" && (dataSend === null || typeof dataSend === "undefined")) {
          method = "GET";
        }

        var ajax = $.ajax({
          async: false,
          method: method,
          url: this.options.url,
          data: dataSend,
          dataType: this.options.dataType,
          success: function (xhr) {
            _ajax = xhr;
          },
          error: function (xhr, statusText, errorThrown) {
            _ajax = {
              error: true,
              status: xhr && xhr.status ? xhr.status : 0,
              statusText: statusText || "",
              message: errorThrown || "Request failed",
              responseText: xhr && xhr.responseText ? xhr.responseText : "",
            };
          },
        });
        if (_ajax === null) {
          return { error: true, status: 0, statusText: "", message: "Request failed", responseText: "" };
        }
        return _ajax;
      } else {
        return this.options.content();
      }
    },
    render: function (data) {
      var header = $(".modal-title", this.options.wrapper);
      var wrapper = $(".modal-content", this.options.wrapper);

      var _data = this.options.render(wrapper, data);

      $(wrapper).html(_data);
      $(header).html(this.options.header);
    },
    finish: function (el) {
      this.options.finish(el);
    },
    close: function (el) {
      $(this.options.wrapper).removeClass("open");
      $(this.options.blocker).removeClass("open");
      $("body").removeClass("modal-open");
    },
    destroy: function (el) {
      $(".modal").unbind("model");
      $(this.element).removeData(pluginName);
    },
  };

  $.fn[pluginName] = function (options, params) {
    $(this).each(function () {
      if (!$.data(this, pluginName)) {
        $.data(this, pluginName, new Plugin(this, options, params));
      }
    });

    // chain jQuery functions
    return this;
  };
})(jQuery, window, document);
