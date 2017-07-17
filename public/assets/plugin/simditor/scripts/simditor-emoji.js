(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define('simditor-emoji', ["jquery","simditor"], function (a0,b1) {
      return (root['EmojiButton'] = factory(a0,b1));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"),require("Simditor"));
  } else {
    root['SimditorEmoji'] = factory(root["jQuery"],root["Simditor"]);
  }
}(this, function ($, Simditor) {

var EmojiButton,
  __hasProp = {}.hasOwnProperty,
  __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
  __slice = [].slice;

EmojiButton = (function(_super) {
  __extends(EmojiButton, _super);

  EmojiButton.i18n = {
    'zh-CN': {
      emoji: '表情'
    },
    'en-US': {
      emoji: 'emoji'
    }
  };

  EmojiButton.images = ['smile.png', 'smiley.png', 'laughing.png', 'blush.png', 'heart_eyes.png', 'smirk.png', 'flushed.png', 'grin.png', 'wink.png', 'kissing_closed_eyes.png', 'stuck_out_tongue_winking_eye.png', 'stuck_out_tongue.png', 'sleeping.png', 'worried.png', 'expressionless.png', 'sweat_smile.png', 'cold_sweat.png', 'joy.png', 'sob.png', 'angry.png', 'mask.png', 'scream.png', 'sunglasses.png', 'heart.png', 'broken_heart.png', 'star.png', 'anger.png', 'exclamation.png', 'question.png', 'zzz.png', 'thumbsup.png', 'thumbsdown.png', 'ok_hand.png', 'punch.png', 'v.png', 'clap.png', 'muscle.png', 'pray.png', 'skull.png', 'trollface.png'];

  EmojiButton.prototype.name = 'emoji';

  EmojiButton.prototype.icon = 'smile-o';

  EmojiButton.prototype.menu = true;

  function EmojiButton() {
    var args;
    args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
    EmojiButton.__super__.constructor.apply(this, args);
    $.merge(this.editor.formatter._allowedAttributes['img'], ['data-emoji', 'alt']);
  }

  EmojiButton.prototype.renderMenu = function() {
    var $list, dir, html, name, opts, src, tpl, _i, _len, _ref;
    tpl = '<ul class="emoji-list">\n</ul>';
    opts = $.extend({
      imagePath: 'images/emoji/',
      images: EmojiButton.images
    }, this.editor.opts.emoji || {});
    html = "";
    dir = opts.imagePath.replace(/\/$/, '') + '/';
    _ref = opts.images;
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
      name = _ref[_i];
      src = "" + dir + name;
      name = name.split('.')[0];
      html += "<li data-name='" + name + "'><img src='" + src + "' width='20' height='20' alt='" + name + "' /></li>";
    }
    $list = $(tpl);
    $list.html(html).appendTo(this.menuWrapper);
    return $list.on('mousedown', 'li', (function(_this) {
      return function(e) {
        var $img;
        _this.wrapper.removeClass('menu-on');
        if (!_this.editor.inputManager.focused) {
          return;
        }
        $img = $(e.currentTarget).find('img').clone().attr({
          'data-emoji': true,
          'data-non-image': true
        });
        _this.editor.selection.insertNode($img);
        _this.editor.trigger('valuechanged');
        _this.editor.trigger('selectionchanged');
        return false;
      };
    })(this));
  };

  EmojiButton.prototype.status = function() {};

  return EmojiButton;

})(Simditor.Button);

Simditor.Toolbar.addButton(EmojiButton);

return EmojiButton;

}));
