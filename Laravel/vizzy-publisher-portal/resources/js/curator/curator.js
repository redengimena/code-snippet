(function($) {
  /* "use strict" */

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var cards = null,
  dirty = false,
  vizzy_status = null,
  mediaBrowserTarget = null,

  init = function () {

    vizzy_status = $('#vizzy_status').text();

    setupMediaBrowser()
    setupVizzyImageTile()
    setupAddCardBubble()
    loadCards()
    refresh()

    $('select').selectpicker();

    window.onbeforeunload = function() {
      if (dirty) {
        return 'You have unsaved data. Are you sure you want to leave?';
      }
    };
  },

  disablePublishing = function() {
      $('#publishForm input[type=submit]').prop('disabled',true);
      $('#publishForm input[type=submit]').attr('title', 'Please save changes before publishing');
  }

  setupMediaBrowser = function() {
    $('#media_manager_output').on('change', function(){
      let img = $(this);
      setTimeout(function() {
        let src = img.val();
        mediaBrowserTarget.find('input[type=hidden]').val(src);
        mediaBrowserTarget.find('.img-preview').attr('src', src);
        mediaBrowserTarget.find('.btn-media-remove').removeClass('d-none');
        if (mediaBrowserTarget.closest('#addCardModal').length) {
          // if in add card modal, we should check interaction restriction
          $('#addCardModal .btn-next').prop('disabled', false);
          restrictInteractionTab();
        }
        dirty=true;
        disablePublishing();
      }, 500);
    });
  },

  setupVizzyImageTile = function () {
    $('#details').on('click', '.btn-media-manager', function(){
      mediaBrowserTarget = $(this).closest('.media-browser-target');
      $('#media_manager').trigger('click');
    });

    $('#details').on('click', '.btn-media-remove', function(){
      mediaBrowserTarget = $(this).closest('.media-browser-target');
      mediaBrowserTarget.find('.img-preview').attr('src','');
      mediaBrowserTarget.find('input[type=hidden]').val('');
      $(this).addClass('d-none');
    });
  },

  loadCards = function() {
    let cardsJSON = $('input[name=cards]').val();
    if (cardsJSON) {
      cards = JSON.parse(cardsJSON);
    } else {
      cards = [];
    }
  },

  refresh = function() {
    refreshCueList()
    refreshTimeline()
    refreshPreview()
  },

  resetAddCardModal = function() {
    $('#addCardModal').find('input[name=start_time]').val();
    $('#addCardModal').find('input[name=end_time]').val('');
    $('#addCardModal').find('input[name=vcard_title]').val('');
    $('#addCardModal').find('textarea[name=vcard_content]').val('');
    $('#addCardModal').find('input[name=vcard_cover]').val('');
    $('#addCardModal').find('.img-preview').attr('src', '');
    $('#addCardModal').find('.btn-media-remove').addClass('d-none');
    $('#addCardModal').find('.btn-next').prop('disabled', true);
    $('#addCardModal').find('.btn-save').data('id', false);
    $('#cardDetails').collapse('show');
    $('#cardDetails').prev().removeClass('collapsed');
    $('#trayDetails').prev().addClass('collapsed');
    resetModalInfo();
    resetModalSocial();
    resetModalProduct();
    resetModalWeb();
    restrictInteractionTab();
  },

  resetModalInfo = function(){
    $('#info-form').find('input[name=info_title]').val('');
    $('#info-form').find('input[name=info_image]').val('');
    $('#info-form').find('textarea[name=info_content]').val('');
  },

  resetModalSocial = function() {
    $('#social-form').find('.social-group').remove();

    let group = $('#social-template .social-group').clone();
    group.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
    $('#social-form').append(group);
    group.find('select').selectpicker();
  },

  resetModalProduct = function() {
    $('#product-form').find('select').selectpicker('val', '');
    $('#product-form').find('input[name=product_title]').val('');
    $('#product-form').find('input[name=product_image]').val('');
    $('#product-form').find('textarea[name=product_content]').val('');
    $('#product-form').find('input[name=product_url]').val('');
  },

  resetModalWeb = function() {
    $('#web-form .web-group').remove();

    let group = $('#web-template .web-group').clone();
    group.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
    $('#web-form').append(group);
    group.find('select').selectpicker();
  },

  setupAddCardBubble = function() {
    Amplitude.init({songs: [{"url": audio}],callbacks:{timeupdate:function(){ setTimeout(timeupdate(),1000); }}});

    document.getElementById('song-played-progress').addEventListener('click', function( e ){
      var offset = this.getBoundingClientRect();
      var x = e.pageX - offset.left;

      Amplitude.setSongPlayedPercentage( ( parseFloat( x ) / parseFloat( this.offsetWidth) ) * 100 );
    });

    const allRanges = document.querySelectorAll(".song-navigation");
    allRanges.forEach(wrap => {
      const range = wrap.querySelector(".range");
      const bubble = wrap.querySelector(".bubble");
      if (bubble) {
        range.addEventListener("input", () => {
          setBubble(range, bubble);
        });
        setBubble(range, bubble);
      }
    });

    $('.add-card').on('click', function(){
      Amplitude.pause();
      $('.amplitude-play-pause').removeClass('amplitude-playing').addClass('amplitude-paused');
      var MHSTime = secondsToTime(Amplitude.getSongPlayedSeconds());
      resetAddCardModal();
      $('#addCardModal').modal();
      $('#addCardModal').find('input[name=start_time]').val(MHSTime);
      $('#trayDetails a[href="#info"]').trigger('click');
    });

    $('#addCardModal .accordion__header--close').on('click', function(){
      $('#addCardModal').modal('hide');
    });

    $('#addCardModal').on('click', '.btn-media-manager', function(){
      mediaBrowserTarget = $(this).closest('.media-browser-target');
      $('#media_manager').trigger('click');
    });

    $('#addCardModal').on('click', '.btn-media-remove', function(){
      mediaBrowserTarget = $(this).closest('.media-browser-target');
      mediaBrowserTarget.find('.img-preview').attr('src','');
      mediaBrowserTarget.find('input[type=hidden]').val('');
      $(this).addClass('d-none');
      restrictInteractionTab();
    });

    $('#addCardModal .btn-next').on('click', function(){
      if (validateTime() && validateCardTitle()) {
        $('#trayDetails').collapse('show');
        $('#trayDetails').prev().removeClass('collapsed');
        $('#cardDetails').prev().addClass('collapsed');
      }
    });

    $('#addCardModal .accordion__header--indicator').on('click', function(){
      if ($('#trayDetails').prev().hasClass('collapsed')) {
        $('#trayDetails').collapse('show');
        $('#trayDetails').prev().removeClass('collapsed');
        $('#cardDetails').prev().addClass('collapsed');
      } else {
        $('#cardDetails').collapse('show');
        $('#cardDetails').prev().removeClass('collapsed');
        $('#trayDetails').prev().addClass('collapsed');
      }
    });

    $('#addCardModal .btn-prev').on('click', function(){
      $('#cardDetails').collapse('show');
      $('#cardDetails').prev().removeClass('collapsed');
      $('#trayDetails').prev().addClass('collapsed');
    });

    $('#addCardModal').on('change', 'input, textarea, select', function(){
      restrictInteractionTab();
    });

    $('#addCardModal .btn-add-social').on('click', function(){
      let group = $('#social-template').find('.social-group').clone();
      group.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
      $('#social-form').append(group);
      group.find('select').selectpicker();
      $(this).remove();
    });

    $('#addCardModal').on('click', '.btn-add-social-link', function(e){
      e.preventDefault();
      let group = $(this).closest('.social-group');
      if (group.find('.social-link').length < 4) {
        let link = $('#social-template').find('.social-link').clone();
        link.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
        group.find('.social-links').append(link);
        link.find('select').selectpicker();
      }
    });

    $('#addCardModal').on('click', '.btn-add-web-link', function(e){
      e.preventDefault();
      let group = $(this).closest('.web-group');
      if (group.find('.web-link').length < 10) {
        let link = $('#web-template').find('.web-link').clone();
        link.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
        group.find('.web-links').append(link);
        link.find('select').selectpicker();
      }
    });


    $('#addCardModal .btn-save').on('click', function(){
      if (validateTime() && validateInteractions()) {
        let img = $('#addCardModal').find('input[name=vcard_cover]').val();
        let title = $('#addCardModal').find('input[name=vcard_title]').val();
        let content = $('#addCardModal').find('textarea[name=vcard_content]').val();
        let start = $('#addCardModal').find('input[name=start_time]').val();
        let end = $('#addCardModal').find('input[name=end_time]').val();
        let interactions = saveInteractions();
        let idx = $('#addCardModal').find('.btn-save').data('id');
        if (idx !== false) {
          cards.splice(idx,1);
        }
        addCard(title, content, img, timeToSeconds(start), timeToSeconds(end), interactions);
        dirty = true;
        disablePublishing();
        $('#addCardModal').modal('hide');
      }
    });

    $('#deleteCardModal .btn-primary').on('click', function(){
      cards.splice($('#deleteCardModal').data('id'),1);
      refresh();
      $('#deleteCardModal').modal('hide');
    });

    $('#saveForm').on('submit', function() {
      dirty = false;
      $('input[name=vizzy_image]').val($('input[name=vizzy_cover]').val());
      $('input[name=cards]').val(JSON.stringify(cards));
    });

  },

  setBubble = function(range, bubble) {
    const val = range.value;
    const min = range.min ? range.min : 0;
    const max = range.max ? range.max : 100;
    const newVal = Number(((val - min) * 100) / (max - min));
    bubble.style.left = `calc(${newVal}% + (${8 - newVal * 0.15}px))`;
  },

  timeupdate = function () {
    const wrap = document.querySelector(".song-navigation");
    const range = wrap.querySelector(".range");
    const bubble = wrap.querySelector(".bubble");
    setBubble(range, bubble);

    refreshPreview();
  },

  refreshPreview = function() {
    let sec = parseInt(Amplitude.getSongPlayedSeconds());
    let card = getCardBySecond(sec);
    resetPreview();
    if (card) { showPreview(card); }
  },

  getCardBySecond = function(sec) {
    let active = false;
    $.each(cards, function(idx, card) {
      if (card.start <= sec) { active = card; }
      if (card.end && card.end < sec) { active = false; }
      if (card.start > sec) { return false; }
    });
    return active;
  },

  openCardModal = function(idx,card) {
    resetAddCardModal();
    $('#addCardModal').modal();
    $('#addCardModal').find('input[name=vcard_title]').val(card.title);
    $('#addCardModal').find('textarea[name=vcard_content]').val(card.content);
    $('#addCardModal').find('input[name=start_time]').val(secondsToTime(card.start));
    $('#addCardModal').find('input[name=end_time]').val(card.end ? secondsToTime(card.end) : '');
    $('#addCardModal').find('input[name=vcard_cover]').val(card.image);
    $('#addCardModal').find('.card-image').attr('src', card.image);
    $('#addCardModal').find('.btn-next').prop('disabled', false);
    $('#addCardModal').find('.btn-save').data('id', idx);
    $('#cardDetails').collapse('show');
    $('#cardDetails').prev().removeClass('collapsed');
    $('#trayDetails').prev().addClass('collapsed');
    loadModalInteractions(card);
    restrictInteractionTab();
  },

  restrictInteractionTab = function () {
    let interactions = saveInteractions();
    let used = [], unused = [];
    Object.entries(interactions).forEach(([key, value]) => {
      if (value) {
        used.push(key);
      } else {
        unused.push(key);
      }
    });
    if (used.length > 2) {
      unused.forEach((key) => {
        $('#trayDetails .nav-item a[href="#'+ key +'"]').addClass('disabled');
      });
    } else {
      $('#trayDetails .nav-item a').removeClass('disabled');
    }
  },

  loadModalInteractions = function(card) {
    let clicked = false;
    if (card.interactions.info) {
      loadModalInfo(card.interactions.info);
      if (!clicked) { $('#trayDetails a[href="#info"').trigger('click'); clicked=true; }
    }
    if (card.interactions.social) {
      loadModalSocial(card.interactions.social);
      if (!clicked) { $('#trayDetails a[href="#social"').trigger('click'); clicked=true; }
    }
    if (card.interactions.product) {
      loadModalProduct(card.interactions.product);
      if (!clicked) { $('#trayDetails a[href="#product"').trigger('click'); clicked=true; }
    }
    if (card.interactions.web) {
      loadModalWeb(card.interactions.web);
      if (!clicked) { $('#trayDetails a[href="#web"').trigger('click'); clicked=true; }
    }
  },

  loadModalInfo = function(info) {
    $('#info-form').find('input[name=info_title]').val(info.title);
    $('#info-form').find('input[name=info_image]').val(info.image);
    $('#info-form').find('.img-preview').attr('src',info.image);
    $('#info-form').find('textarea[name=info_content]').val(info.content);
  }

  loadModalSocial = function(socials) {
    $('#social-form .social-group').remove();
    $.each(socials, function(idx,social){
      let group = $('#social-template').find('.social-group').clone();
      group.find('input[name=social_title]').val(social.title);

      group.find('.social-link').remove();
      $.each(social.links, function(idx,link){
        let grouplink = $('#social-template').find('.social-link').clone();
        grouplink.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
        grouplink.find('select').selectpicker();
        grouplink.find('select[name=social_type]').selectpicker('val', link.type);
        grouplink.find('input[name=social_url]').val(link.url);
        group.find('.social-links').append(grouplink);
      });
      $('#social-form').append(group);
    })
  },

  loadModalProduct = function(product) {
    $('#product-form').find('select[name=product_type]').selectpicker('val',product.type);
    $('#product-form').find('input[name=product_title]').val(product.title);
    $('#product-form').find('input[name=product_image]').val(product.image);
    $('#product-form').find('.img-preview').attr('src',product.image);
    if (product.image) {
      $('#product-form').find('.btn-media-remove').removeClass('d-none');
    }
    $('#product-form').find('textarea[name=product_content]').val(product.content);
    $('#product-form').find('input[name=product_url]').val(product.url);
  }

  loadModalWeb = function(webs) {
    $('#web-form .web-group').remove();
    $.each(webs, function(idx,web){
      let group = $('#web-template').find('.web-group').clone();
      group.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
      group.find('select').selectpicker();
      group.find('input[name=web_title]').val(web.title);
      group.find('input[name=web_content]').val(web.content);
      group.find('input[name=web_image]').val(web.image);
      group.find('.img-preview').attr('src',web.image);
      if (web.image) {
        group.find('.btn-media-remove').removeClass('d-none');
      }
      group.find('.web-link').remove();
      $.each(web.links, function(idx,link){
        let grouplink = $('#web-template').find('.web-link').clone();
        grouplink.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
        grouplink.find('select').selectpicker();
        grouplink.find('select[name=web_type]').selectpicker('val', link.type);
        grouplink.find('input[name=web_url]').val(link.url);
        group.find('.web-links').append(grouplink);
      });
      $('#web-form').append(group);
    })
  },

  validateCardTitle = function() {
    $('input[name=vcard_title]').removeClass('is-invalid');
    if (!$('input[name=vcard_title]').val()) {
      $('input[name=vcard_title]').addClass('is-invalid');
      return false;
    }
    return true;
  },

  validateTime = function() {
    $('input[name=start_time]').removeClass('is-invalid');
    $('input[name=end_time]').removeClass('is-invalid');
    let start = $('#addCardModal').find('input[name=start_time]').val();
    let end = $('#addCardModal').find('input[name=end_time]').val();
    if (timeToSeconds(start) < 0) {
      $('#start_time-error').text('Invalid start time');
      $('input[name=start_time]').addClass('is-invalid');
      $('#cardDetails').collapse('show');
      return false;
    }
    if (end && timeToSeconds(end) < 0) {
      $('#end_time-error').text('Invalid end time');
      $('input[name=end_time]').addClass('is-invalid');
      $('#cardDetails').collapse('show');
      return false;
    }
    if (timeToSeconds(start) > 0 && timeToSeconds(end) > 0 && (timeToSeconds(end) < timeToSeconds(start))) {
      $('#end_time-error').text('End time must be bigger than start time');
      $('input[name=end_time]').addClass('is-invalid');
      $('#cardDetails').collapse('show');
      return false;
    }
    return true;
  },

  secondsToTime = function(seconds) {
    var measuredTime = new Date(null);
    measuredTime.setSeconds(seconds);
    return measuredTime.toISOString().substr(11, 8).replace('00:','');
  },

  timeToSeconds = function(time) {
    if (!/^[0-9:]*$/.test(time)) {
      return -1;
    }
    let t = time.split(':')
    if (t.length > 3) { return -1; }
    if (t.length == 3) {
      if (t[1] > 60 || t[2] > 60) { return -1; }
      return (+t[0]) * 60 * 60 + (+t[1]) * 60 + (+t[2])
    }
    if (t.length == 2) {
      if (t[1] > 60) { return -1; }
      return (+t[0]) * 60 + (+t[1])
    }
    if (t.length == 1) { return (+t[0]) }
    return -1
  },

  validateInteractions = function() {
    if (!validateSocial()) {
      $('a[href="#social"]').trigger('click');
      return false;
    }
    if (!validateProduct()) {
      $('a[href="#product"]').trigger('click');
      return false;
    }
    if (!validateWeb()) {
      $('a[href="#web"]').trigger('click');
      return false;
    }
    return true;
  },

  validateSocial = function() {
    let error = false;
    $('#social-form .error').remove();
    $('#social-form .social-group').each(function(){
        let group = $(this);
        let title = group.find('input[name=social_title]').val();
        let links = false;
        group.find('.social-link').each(function(){
          let type = $(this).find('select[name=social_type]').val();
          let url = $(this).find('input[name=social_url]').val();
          if (type && url) { links = true; }
          if (type && !url) {
            errorMessage('Required').insertAfter($(this).find('input[name=social_url]'));
            error = true;
          }
          if(!type && url){
            errorMessage('Social Media type is required').insertAfter($(this).find('.bootstrap-select'));
            error = true;
          }
        });
        if (title && !links) {
          errorMessage('Group does not have any links').insertAfter(group.find('input[name=social_title]'));
          error = true;
        }
        if (!title && links) {
          errorMessage('Required').insertAfter(group.find('input[name=social_title]'));
          error = true;
        }
    });
    return !error;
  },

  validateProduct = function() {
    $('#product-form .error').remove();
    let form = $('#product-form');
    let error = false;
    let type = form.find('select[name=product_type]').val();
    let title = form.find('input[name=product_title]').val();
    let image = form.find('input[name=product_image]').val();
    let content = form.find('textarea[name=product_content]').val();
    let url = form.find('input[name=product_url]').val();
    if (title || image || content) {
      if (!type) { errorMessage('Required').insertAfter(form.find('.bootstrap-select')); error=true;}
      if (!url) { errorMessage('Required').insertAfter(form.find('input[name=product_url]')); error=true;}
    } else {
      if (!type && url) {
        errorMessage('Required').insertAfter(form.find('.bootstrap-select'));
        error=true;
      }
      if (type && !url) {
        errorMessage('Required').insertAfter(form.find('input[name=product_url]'));
        error=true;
      }
    }

    return !error;
  },

  validateWeb = function() {
    let error = false;
    $('#web-form .error').remove();
    $('#web-form .web-group').each(function(){
      let group = $(this);
      let title = group.find('input[name=web_title]').val();
      let image = group.find('input[name=web_image]').val();
      let content = group.find('textarea[name=web_content]').val();
      let links = false;
      group.find('.web-link').each(function(){
        let type = $(this).find('select[name=web_type]').val();
        let url = $(this).find('input[name=web_url]').val();
        if (type && url) { links = true; }
        if (type && !url) {
          errorMessage('Required').insertAfter($(this).find('input[name=web_url]'));
          error = true;
        }
        if(!type && url){
          errorMessage('Web link type is required').insertAfter($(this).find('.bootstrap-select'));
          error = true;
        }
      });
      if ((image || content) && !title) {
        errorMessage('Required').insertAfter(group.find('input[name=web_title]'));
        error = true;
      }
      if (title && !links) {
        errorMessage('Group does not have any links').insertAfter(group.find('input[name=web_title]'));
        error = true;
      }
      if (!title && links) {
        errorMessage('Required').insertAfter(group.find('input[name=web_title]'));
        error = true;
      }
    });

    return !error;
  },

  errorMessage = function(msg) {
    return $('<div class="error text-danger small"></div>').text(msg);
  },

  saveInteractions =function() {
    return {
      'info': saveInfo(),
      'social': saveSocial(),
      'product': saveProduct(),
      'web': saveWeb(),
    }
  },

  saveInfo = function() {
    let title = $('#info-form input[name=info_title]').val();
    let image = $('#info-form input[name=info_image]').val();
    let content = $('#info-form textarea[name=info_content]').val();

    if (title || image || content) {
      return {
        'title': title,
        'image': image,
        'content': content
      }
    }else {
      return false;
    }
  },

  saveSocial = function() {
    let social = [];
    $('#social-form .social-group').each(function(){
      let group = $(this);
      let title = group.find('input[name=social_title]').val();
      if (title) {
        let item = {
          'title': title,
          'links': []
        }
        group.find('.social-link').each(function(){
          let type = $(this).find('select[name=social_type]').val();
          let url = $(this).find('input[name=social_url]').val();
          if (type && url) {
            item.links.push({
              'type': type,
              'url': url
            });
          }
        });
        social.push(item);
      }
    });
    return social.length ? social : false;
  },

  saveProduct = function() {
    let form = $('#product-form');
    let type = form.find('select[name=product_type]').val();
    let title = form.find('input[name=product_title]').val();
    let image = form.find('input[name=product_image]').val();
    let content = form.find('textarea[name=product_content]').val();
    let url = form.find('input[name=product_url]').val();

    if (type && url) {
      return {
        'type': type,
        'url': url,
        'title': title,
        'image': image,
        'content': content,
      }
    }

    return false;
  },

  saveWeb = function() {
    let web = [];
    $('#web-form .web-group').each(function(){
      let group = $(this);
      let title = group.find('input[name=web_title]').val();
      let image = group.find('input[name=web_image]').val();
      let content = group.find('textarea[name=web_content]').val();
      if (title) {
        let item = {
          'title': title,
          'image': image,
          'content': content,
          'links': []
        }
        group.find('.web-link').each(function(){
          let type = $(this).find('select[name=web_type]').val();
          let url = $(this).find('input[name=web_url]').val();
          if (type && url) {
            item.links.push({
              'type': type,
              'url': url
            });
          }
        });
        web.push(item);
      }
    });

    return web.length ? web : false;
  },

  addCard = function(title, content, img, start, end, interactions) {
    cards.push({
      'title': title,
      'content': content,
      'start': start,
      'end': end,
      'image': img,
      'interactions': interactions
    })
    autosave();

    refresh();
  },

  autosave = function() {
    $.ajax({
        url: window.location.pathname + '/autosave',
        type: 'POST',
        data: {
          'guid': $('#saveForm input[name=guid]').val(),
          'cards': JSON.stringify(cards)
        },
    }).done(function(data) {
    })
  },

  refreshCueList = function() {
    cards.sort((a,b) => (a.start > b.start) ? 1 : ((b.start > a.start) ? -1 : 0))
    $('.cue-list-panel').html('');
    $.each(cards, function(idx,card) {
      let m = $('<li class="media mb-4">').data('id', idx);
      let imgFrame = $('<div class="img-frame mr-3">');
      let vizzyCardImage = $('<div class="vizzy-card-image">');
      let img = $('<img class="" role="button">').attr('src', card.image);
      let body = $('<div class="media-body">');
      let start = $('<div class="small mb-2">').text(timeDisplay(card));
      let buttons = $('<div class="buttons text-right">');
      let edit = $('<a href="#" class="btn btn-xxs btn-primary mr-1">Edit</a>');
      let del = $('<a href="#" class="btn btn-xxs btn-outline-primary">Delete</a>');
      img.on('click', function(e){ setPlayedPosition(card.start); })
      edit.on('click', function(e){ e.preventDefault(); openCardModal(idx,card); })
      del.on('click', function(e){ e.preventDefault();
        $('#deleteCardModal').modal();
        $('#deleteCardModal').data('id',idx);
      })
      imgFrame.append(vizzyCardImage.append(img));
      m.append(imgFrame);
      m.append(body);
      m.append(buttons);
      body.append($('<h5 class="small">').text(card.title));
      if (card.content) {
        body.append($('<div class="small">').text(card.content));
      }
      buttons.append(start);
      if (vizzy_status != 'Published' && vizzy_status != 'Pending Approval') {
        buttons.append(edit);
        buttons.append(del);
      }
      $('.cue-list-panel').append(m);
    })
  },

  refreshTimeline = function() {
    let duration = Amplitude.getSongDuration();
    if (!duration) {
      setTimeout(refreshTimeline,500);
    } else {
      $('.timeline-thumbnails').html('');
      $.each(cards, function(idx,card) {
        let thumb = $('<div class="slider">');
        let handle = $('<div class="slider-handle ui-slider-handle"></div>');
        let time = $('<div class="time small text-white"></div>');
        let img = $('<img width="28" height="30">').attr('src', card.image);
        let timeText = $('<span></span>');
        let input = $('<input type="text"></input>').hide();
        timeText.text(secondsToTime(card.start));
        if (vizzy_status != 'Published' && vizzy_status != 'Pending Approval') {
          timeText.on('click', function(){
            thumb.slider('disable');
            timeText.hide();
            input.val(timeText.text());
            input.show();
            input.trigger('focus');
          });
          input.on('focusout keyup',function(e) {
            if (e.type === 'focusout' || e.which === 13)  {
              thumb.slider('enable');
              if (timeToSeconds(input.val()) >= 0 && timeToSeconds(input.val()) != card.start){
                card.start = timeToSeconds(input.val());
                card.end = 0;
                dirty = true;
                disablePublishing();
                refresh();
              }
              input.hide();
              timeText.show();
            }
          });
        }
        time.append(timeText);
        time.append(input);
        handle.append(time);
        handle.append(img);
        thumb.append(handle);
        if (vizzy_status != 'Published' && vizzy_status != 'Pending Approval') {
          thumb.slider({value: card.start, max: Amplitude.getSongDuration(),
            slide: function( event, ui ) {
              timeText.text(secondsToTime(ui.value));
              input.val(secondsToTime(ui.value));
            },
            change: function( event, ui ) {
              if (ui.value != card.start) {
                card.start = ui.value;
                dirty = true;
                disablePublishing();
                refreshCueList();
                refreshPreview()
              }
            },
          });
        } else {
          thumb.slider({value: card.start, max: Amplitude.getSongDuration()});
          thumb.slider('disable');
        }
        $('.timeline-thumbnails').append(thumb);
      })
    }
  },

  timeDisplay = function(card) {
    return secondsToTime(card.start) + (card.end ? ' - ' + secondsToTime(card.end) : '')
  }

  showPreview = function(card) {
    $('.main-panel #cardDetailPreview').removeClass('d-none');
    $('.main-panel .vcard-title').text(card.title);
    $('.main-panel .vcard-description').text(card.content);
    $('.main-panel .vcard-time').text(timeDisplay(card));
    $('.main-panel img.vizzy-card-img').attr('src', card.image);
    $('.main-panel img.vizzy-card-img').removeClass('d-none');
    $('.main-panel .vizzy-card-tray img.tray-interation').remove();

    showPreviewInfo(card.interactions.info);
    showPreviewSocial(card.interactions.social);
    showPreviewProduct(card.interactions.product);
    showPreviewWeb(card.interactions.web);

    selectFirstAvailableInteractionTab(card.interactions);
  },

  showPreviewInfo = function(info) {
    if (info) {
      $('#info-preview-tab').removeClass('d-none');
      let media = $('<div class="media">');
      let body = $('<div class="media-body">');
      if (info.image) {
        media.append($('<img class="mr-3" width="60">').attr('src', info.image));
      }
      if (info.title) {
        body.append($('<h5>').text(info.title));
      }
      if (info.content) {
        body.append($('<div>').text(info.content));
      }
      if (info.content || info.title) {
        media.append(body);
      }
      $('#info-preview').append(media);

      let icon= s3_url + 'info.png';
      $('.vizzy-card-tray').append('<img class="tray-info tray-interation" src="'+icon+'" />');
    }
  },

  showPreviewSocial = function(social) {
    if (social) {
      let icon = '';
      $('#social-preview-tab').removeClass('d-none');

      $.each(social, function(idx,group) {
        let wrapper = $('<div class="mb-4">');
        wrapper.append($('<h5>').text(group.title));
        $.each(group.links, function(idx,link) {
          icon = (icon != '' && icon != link.type) ? 'social' : link.type;
          icon = (icon == 'other') ? 'social' : icon;
          let media = $('<div class="media mb-1">');
          let body = $('<div class="media-body">');
          // let img = $('<span>').addClass('sharp text-center mr-3 btn-' + link.type);
          // img.append($('<i>').addClass('fa fa-' + link.type));
          let img = $('<img width="40" height="40" class="mr-2">').attr('src', s3_url + link.type + '.png');
          media.append(img);
          media.append(body);
          body.append($('<span>').text(link.url));
          wrapper.append(media);
        });
        $('#social-preview').append(wrapper);
      });

      $('.vizzy-card-tray').append('<img class="tray-social tray-interation" src="'+ s3_url + icon +'.png" />');
    }
  },

  showPreviewProduct = function(product) {
    if (product) {
      let icon = (product.type != 'other') ? product.type : 'product';
      $('#product-preview-tab').removeClass('d-none');
      let media = $('<div class="media">');
      let body = $('<div class="media-body">');
      if (product.image) {
        media.append($('<img class="mr-3" width="60">').attr('src', product.image));
      }
      let img = $('<img width="40" height="40" class="mr-2">').attr('src', s3_url + product.type + '.png');
      media.append(img);
      body.append($('<div>').text(product.type).addClass('small'));
      if (product.title) {
        body.append($('<h5>').text(product.title));
      }
      if (product.content) {
        body.append($('<div>').text(product.content));
      }
      if (product.content || product.title) {
        media.append(body);
      }
      media.append($('<a class="btn btn-xxs btn-primary">').attr('href',product.url).text('Buy'));
      $('#product-preview').append(media);

      $('.vizzy-card-tray').append('<img class="tray-product tray-interation" src="'+ s3_url + icon +'.png" />');
    }
  },

  showPreviewWeb = function(webs) {
    if (webs) {
      let trayicon = '';
      $('#web-preview-tab').removeClass('d-none');
      $.each(webs, function(idx,group){
        let media = $('<div class="media mb-2">');
        let body = $('<div class="media-body">');
        if (group.image) {
          media.append($('<img class="mr-3" width="60">').attr('src', group.image));
        }
        if (group.title) {
          body.append($('<h5>').text(group.title));
        }
        if (group.content) {
          body.append($('<div>').text(group.content));
        }
        if (group.content || group.title) {
          media.append(body);
        }
        let wrapper = $('<div class="mb-4">');
        $.each(group.links, function(idx,link) {
          trayicon = (trayicon != '' && trayicon != link.type) ? 'web' : link.type;
          trayicon = (link.type == 'other') ? 'web' : trayicon;
          let media2 = $('<div class="media mb-1">');
          let body2 = $('<div class="media-body">');
          // let icon = $('<span>').addClass('sharp text-center mr-3 bg-primary text-white');
          // icon.append($('<i>').addClass('fa fa-globe'));
          let icon = $('<img width="40" height="40" class="mr-2">').attr('src', s3_url + link.type + '.png');
          media2.append(icon);
          media2.append(body2);
          body2.append($('<span>').text(link.url));
          wrapper.append(media2);
        });
        $(body).append(wrapper);
        $('#web-preview').append(media);

        $('.vizzy-card-tray').append('<img class="tray-web tray-interation" src="'+ s3_url + trayicon +'.png" />');
      });
    }
  },

  selectFirstAvailableInteractionTab = function(interactions) {
    if (interactions.info || interactions.social || interactions.product || interactions.web) {
      showPreviewInteractions();
    }
    if (interactions.info) {
      $('#info-preview-tab a').trigger('click');
    } else if (interactions.social) {
      $('#social-preview-tab a').trigger('click');
    } else if (interactions.product) {
      $('#product-preview-tab a').trigger('click');
    } else if (interactions.web) {
      $('#web-preview-tab a').trigger('click');
    }
  },

  showPreviewInteractions = function() {
    $('#preview-interactions .nav-tabs').removeClass('d-none');
  },

  hidePreviewInteractions = function() {
    $('#preview-interactions .nav-tabs').addClass('d-none');
    $('#preview-interactions .nav-item').addClass('d-none');
  },

  resetPreview = function() {
    $('.main-panel #cardDetailPreview').addClass('d-none');
    $('.main-panel .vcard-title').text('');
    $('.main-panel .vcard-description').text('');
    $('.main-panel .vcard-time').text('');
    $('.main-panel .vizzy-card-tray img.tray-interation').remove();
    $('.main-panel img.vizzy-card-img').attr('src', '');
    $('.main-panel img.vizzy-card-img').addClass('d-none');
    $('.main-panel .tray-links').html('');
    hidePreviewInteractions();
    $('#info-preview').html('');
    $('#social-preview').html('');
    $('#product-preview').html('');
    $('#web-preview').html('');
  },


  setPlayedPosition = function(sec) {
    let percentage = parseFloat(sec/Amplitude.getSongDuration() * 100);
    Amplitude.setSongPlayedPercentage( percentage==0 ? 0.0001 : percentage );
  }

  init();
})(jQuery)