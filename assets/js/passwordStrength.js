var UapPasswordStrength = {
  colors: ['#F00', '#F90', '#FF0', '#9F0', '#0F0'],
  labels: [],

  init: function(args){
    var obj = this
    obj.setAttributes(obj, args)
    obj.labels = jQuery.parseJSON(window.uapPasswordStrengthLabels)

    jQuery(document).ready(function(){
        jQuery(document).on('keyup', jQuery('[name=pass1]'), function (evt) {
            obj.handleTypePassword(obj, evt)
        })
        jQuery(document).on('keyup', jQuery('[name=pass2]'), function (evt) {
            obj.handleTypePassword(obj, evt)
        })
    })
  },

  setAttributes: function(obj, args){
      for (var key in args) {
        obj[key] = args[key]
      }
  },

  handleTypePassword: function(obj, evt){
      var rules = jQuery(evt.target).attr('data-rules')
      rules = rules.split(',')
      var strength = obj.mesureStrength(evt.target.value, rules)
      var color = obj.getColor(strength)
      var ul = jQuery(evt.target).parent().find('ul')
      ul.children('li').css({ "background": "#DDD" }).slice(0, color.idx).css({ "background": color.col })

      newLabel = obj.labels[0]
      if (strength>10 && strength<21){
          newLabel = obj.labels[1]
      } else if (strength>20 && strength<31){
          newLabel = obj.labels[2]
      } else if (strength>30){
          newLabel = obj.labels[3]
      }
      jQuery(evt.target).parent().find('.uap-strength-label').html(newLabel)

  },

  mesureStrength: function (p, rules) {

      var _force = 0;
      var _regex = /[$-/:-?{-~!"^_`\[\]]/g;

      var _letters = /[a-zA-Z]+/.test(p);
      var _lowerLetters = /[a-z]+/.test(p);
      var _upperLetters = /[A-Z]+/.test(p);
      var _numbers = /[0-9]+/.test(p);
      var _symbols = _regex.test(p);

      if (p.length<rules[0]){
          return 0
      }
      if (rules[1]==2 && (!_numbers || !_letters )){
          return 0
      } else if (rules[1]==3 && (!_numbers || !_letters || !_upperLetters)){
          return 0
      }

      var _flags = [_lowerLetters, _upperLetters, _numbers, _symbols];
      var _passedMatches = jQuery.grep(_flags, function (el) { return el === true; }).length;

      _force += 2 * p.length + ((p.length >= 10) ? 1 : 0);
      _force += _passedMatches * 10;

      // penality (short password)
      _force = (p.length <= 6) ? Math.min(_force, 10) : _force;

      // penality (poor variety of characters)
      _force = (_passedMatches == 1) ? Math.min(_force, 10) : _force;
      _force = (_passedMatches == 2) ? Math.min(_force, 20) : _force;
      _force = (_passedMatches == 3) ? Math.min(_force, 40) : _force;
      return _force;
  },

  getColor: function (s) {
      var idx = 0;
      if (s <= 10) { idx = 0; }
      else if (s <= 20) { idx = 1; }
      else if (s <= 30) { idx = 2; }
      else if (s <= 40) { idx = 3; }
      else { idx = 4; }
      return { idx: idx + 1, col: this.colors[idx] };
  }

}
UapPasswordStrength.init({})
