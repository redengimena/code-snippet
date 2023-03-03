(function($) {
  /* "use strict" */

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  var cards = null;

  init = function () {

    setupAddCardBubble()
    loadCards()
    refresh()

    setupDropzone()
    setupChapterTitle()
    setupChapterDescr()
    setupChapterUrl()
    setupAudioNameEditable()
    setupShowNotes()

    downloadForm()
  },

  loadCards = function() {
    let cardsJSON = $('input[name=chapters]').val();
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
      addChapter()
    });

  },

  addChapter = function() {
      Amplitude.pause();
      $('.amplitude-play-pause').removeClass('amplitude-playing').addClass('amplitude-paused');
      var MHSTime = secondsToTime(Amplitude.getSongPlayedSeconds());

      $('#card_detail').removeClass('d-none')
      $('#card_detail').data('id', '');
      $('.upload-container').removeClass('d-none')
      $('#upload_error').text('');
      $('#orig_image').attr('src','')
      $('.orig-image').addClass('d-none')
      $('input[name=chapter_name]').val('')
      $('textarea[name=chapter_descr]').val('')
      $('input[name=chapter_url]').val('')
      $('input[name=chapter_start]').val(MHSTime)
      $('.dropzone').trigger('click')
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

    setActiveTimelineThumbnail();
    refreshPreview();
  },

  setActiveTimelineThumbnail = function() {
    let sec = parseInt(Amplitude.getSongPlayedSeconds())
    let card = getCardBySecond(sec)
    $('.timeline-thumbnails .slider').removeClass('active')
    $('.timeline-thumbnails .slider').each(function(){
      if ($(this).data('id') == card.id) {
        $(this).addClass('active')
      }
    })
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
      if (card.start/1000 <= sec) { active = card; }
      if (card.end && card.end/1000 < sec) { active = false; }
      if (card.start/1000 > sec) { return false; }
    });
    return active;
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

  refreshCueList = function() {
    cards.sort((a,b) => (a.start > b.start) ? 1 : ((b.start > a.start) ? -1 : 0))
    $('.cue-list-panel').html('');
    $.each(cards, function(idx,card) {
      let wrapper = $('<div class="card shadow_1 mb-2"><div class="card-body"></div></div>')
      let m = $('<div class="media">').data('id', idx);
      let imgFrame = $('<div class="img-frame mr-3">');
      let vizzyCardImage = $('<div class="apple-image">');
      let img = $('<img class="" role="button">').attr('src', public_url + 'placeholder.png');
      if (card.image) {
        img.attr('src', public_url + card.image);
      }
      let body = $('<div class="media-body">');
      let start = $('<div>').text(timeDisplay(card));
      let buttons = $('<div class="buttons text-right">');
      let dropdown = $('<div class="dropdown"><a href="#" data-toggle="dropdown" ><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18px" height="18px" viewBox="0 0 24 24" version="1.1"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><rect x="0" y="0" width="24" height="24"/><circle fill="#000000" cx="5" cy="12" r="2"/><circle fill="#000000" cx="12" cy="12" r="2"/><circle fill="#000000" cx="19" cy="12" r="2"/></g></svg></a><ul class="dropdown-menu dropdown-menu-right"><li class="dropdown-item"></li></ul></div>')
      let del = $('<a href="#">Delete</a>');
      body.on('click', function(e){ setPlayedPosition(card.start/1000); openCardPanel(card);})
      img.on('click', function(e){ setPlayedPosition(card.start/1000); openCardPanel(card);})
      del.on('click', function(e){ e.preventDefault(); deleteImage(card); })
      imgFrame.append(vizzyCardImage.append(img));
      wrapper.find('.card-body').append(m)
      m.append(imgFrame);
      m.append(body);
      m.append(buttons);
      body.append($('<h5 class="mb-0">').text(card.title));
      body.append(start);
      buttons.append(dropdown);
      dropdown.find('li.dropdown-item').append(del)

      $('.cue-list-panel').append(wrapper);
    })
    let plus = '<i class="ti-plus"></i>'
    let addChapterBox = $('<div class="add-box">' + plus + ' Add chapter</div>')
    addChapterBox.on('click', function(e){ addChapter() })
    $('.cue-list-panel').append(addChapterBox)
  },

  refreshTimeline = function() {
    let duration = Amplitude.getSongDuration();
    if (!duration) {
      setTimeout(refreshTimeline,500);
    } else {
      $('.timeline-thumbnails').html('');
      $.each(cards, function(idx,card) {
        let thumb = $('<div class="slider">');
        thumb.data('id', card.id)
        let handle = $('<div class="slider-handle ui-slider-handle"></div>');
        let time = $('<div class="time small text-white"></div>');
        let img = $('<img width="28" height="28">').attr('src', public_url + 'placeholder.png');
        if (card.image) {
          img.attr('src', public_url + card.image);
        }
        let timeText = $('<span></span>');
        let input = $('<input type="text"></input>').hide();
        timeText.text(secondsToTime(card.start/1000));

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
              if (timeToSeconds(input.val()) >= 0 && timeToSeconds(input.val()) != card.start/1000){
                card.start = timeToSeconds(input.val())*1000;
                dirty = true;
                autosave();
                refresh();
              }
              input.hide();
              timeText.show();
            }
          });

        time.append(timeText);
        time.append(input);
        handle.append(time);
        handle.append(img);
        thumb.append(handle);

        thumb.on('click', function(){setPlayedPosition(card.start/1000); openCardPanel(card);})

          thumb.slider({value: card.start/1000, max: Amplitude.getSongDuration(),
            slide: function( event, ui ) {
              timeText.text(secondsToTime(ui.value));
              input.val(secondsToTime(ui.value));
            },
            change: function( event, ui ) {
              if (ui.value != card.start/1000) {
                card.start = ui.value*1000;
                dirty = true;
                autosave();
                refreshCueList();
                refreshPreview()
              }
            },
          });
        $('.timeline-thumbnails').append(thumb);
      })
    }
  },

  timeDisplay = function(card) {
    return secondsToTime(card.start/1000) + (card.end ? ' - ' + secondsToTime(card.end/1000) : '')
  }

  openCardPanel = function(card) {
    $('#card_detail').removeClass('d-none')
    $('#card_detail').data('id', card.id)
    $('#upload_error').text('');
    if (card.image) {
      $('#orig_image').attr('src', public_url + card.image);
      $('.orig-image').removeClass('d-none');
      $('.upload-container').addClass('d-none')
    } else {
      $('.upload-container').removeClass('d-none')
      $('.orig-image').addClass('d-none');
    }

    $('input[name=chapter_name]').val(card.title)
    $('textarea[name=chapter_descr]').val(card.description)
    $('input[name=chapter_url]').val(card.url)
    $('input[name=chapter_start]').val(secondsToTime(card.start/1000));
  },

  showPreview = function(card) {
    $('#card_preview').removeClass('d-none');
    $('#card_preview .preview-chapter-name').text(card.title);
    if (card.image) {
      $('#card_preview .apple-image img').attr('src', public_url + card.image);
    }
  },

  resetPreview = function() {
    $('#card_preview').addClass('d-none');
    $('#card_preview .preview-chapter-name').text('');
    $('#card_preview .apple-image img').attr('src', '');
  },


  setPlayedPosition = function(sec) {
    let percentage = parseFloat(sec/Amplitude.getSongDuration() * 100);
    Amplitude.setSongPlayedPercentage( percentage==0 ? 0.0001 : percentage );
  },

  setupDropzone = function() {
      $('#edit_image').on('click', function(){
          $('.dropzone').trigger('click')
      });
      Dropzone.options.dropzone =
      {
          dictDefaultMessage: 'Upload an image',
          maxFilesize: 2,
          maxFiles: 1,
          acceptedFiles: ".jpg,.png",
          timeout: 60000,
          init: function() {
            this.on("sending", function(file, xhr, formData) {
              $('#upload_error').text('');
              if ($('#card_detail').data('id')) {
                formData.append("id", $('#card_detail').data('id'))
              }
              formData.append("title", $('input[name=chapter_name]').val())
              formData.append("description", $('textarea[name=chapter_descr]').val())
              formData.append("url", $('input[name=chapter_url]').val())
              var start = $('input[name=chapter_start]').val()
              if (timeToSeconds(start) > 0) {
                  formData.append("start", timeToSeconds(start)*1000)
              }
            });
          },
          success: function (file, response) {
              this.removeFile(file)
              cards = JSON.parse(response.chapters)
              openCardPanel(response.card)
              $('input[name=chapter_name]').trigger('focus')
              if (response.last_updated) {
                  $('#last_updated').text(response.last_updated)
              }
              refresh()
          },
          error: function (file, response) {
              $('#upload_error').text(response);
              this.removeFile(file)
              return false;
          }
      };
  },

  autosave = function() {
    disableButtons(true)
    $('#last_updated').html('<span class="spinner-border spinner-border-sm"></span> Saving');
    $.ajax({
        url: window.location.pathname + '/save',
        type: 'POST',
        data: {
          'chapters': JSON.stringify(cards)
        },
    }).done(function(response) {
        cards = JSON.parse(response.chapters)
        if (response.last_updated) {
            $('#last_updated').text(response.last_updated)
        }
        refresh()
        disableButtons(false)
    })
  },

  setupChapterTitle = function() {
      $('input[name=chapter_name]')
        .on('change', function(){
          let card_id = $('#card_detail').data('id');
          $.each(cards, function(idx, card) {
              if (card_id == card.id) {
                  card.title = $('input[name=chapter_name]').val()
              }
          })
          autosave()
      })
  },

  setupChapterDescr = function() {
      $('textarea[name=chapter_descr]')
        .on('change', function(){
          console.log('textarea changed');
          let card_id = $('#card_detail').data('id');
          $.each(cards, function(idx, card) {
              if (card_id == card.id) {
                  card.description = $('textarea[name=chapter_descr]').val()
              }
          })
          autosave()
      })
  },

  setupChapterUrl = function() {
      $('input[name=chapter_url]')
        .on('change', function(){
          let card_id = $('#card_detail').data('id');
          $.each(cards, function(idx, card) {
              if (card_id == card.id) {
                  card.url = $('input[name=chapter_url]').val()
              }
          })
          autosave()
      })
  },

  disableButtons = function(status) {
    $('#generate_notes').prop('disabled', status)
    $('#download_form input[type=submit]').prop('disabled', status)
  },

  deleteImage = function(card) {
     $.ajax({
        url: window.location.pathname + '/delete-image',
        type: 'POST',
        data: {
          'chapter': JSON.stringify(card)
        },
    }).done(function(response) {
        var image = $('#orig_image').attr('src')
        if (public_url + card.image == image) {
            $('#orig_image').attr('src','')
            $('#card_detail').addClass('d-none')
        }

        cards = JSON.parse(response.chapters)
        if (response.last_updated) {
            $('#last_updated').text(response.last_updated)
        }
        refresh()
    })
  },

  setupAudioNameEditable = function() {
      let bar = $('.dashboard_bar')
      let title = $('.dashboard_bar').text()
      let name_wrapper = $('<span id="audio_name"></span')
      let btn = $('<a id="audio_name_edit"><i class="fa fa-pencil"></i> Edit</a>')
      name_wrapper.text(title)
      bar.html('');
      bar.append(name_wrapper)
      bar.append(btn)
      name_wrapper.editable({trigger : btn, action : "click"}, function(e){
          $.ajax({
              url: window.location.pathname + '/save',
              type: 'POST',
              data: {
                'audio_name': e.value
              },
          }).done(function(response) {
              if (response.last_updated) {
                $('#last_updated').text(response.last_updated)
              }
          })
      });
  },

  setupShowNotes = function() {
      let clipboard = new ClipboardJS('.copy');
      clipboard.on('success', function(e) {
        $('#noteModal .btn.copy').text('Copied!')
      })

      $('#generate_notes').on('click', function(){
        let btn = $(this)

        // wait 0.5 sec before to allow auto save to disable the buttons
        setTimeout(function() {
            if (btn.prop('disabled')) {
              return
            }

            if (formError()) {
              return
            }

            btn.html('<span class="spinner-border spinner-border-sm"></span>');

            $.ajax({
                url: window.location.pathname + '/show-notes',
                type: 'POST',
            }).done(function(response) {
                let text = "Enriched by Vizzy\n\n"
                if (response.chapters) {
                  $.each(JSON.parse(response.chapters), function(idx, card) {
                    text += '('+secondsToTime(card.start/1000)+') '
                    text += card.title + '\n'
                    if (card.description) {
                      text += card.description + '\n'
                    }
                    if (card.shorturl) {
                      text += card.shorturl + '\n'
                    } else if (card.url) {
                      text += card.url + '\n'
                    }
                  })
                }
                text += "\nGet your vizzy on podcasters.vizzy.fm"

                $('#generate_notes').html('Generate Show Notes')
                $('#show_notes').val(text)
                $('#noteModal .btn.copy').text('Copy Text')
                $('#noteModal').modal()
            }).fail(function() {
                $('#errorModal .modal-body').html('Error generating show notes. Please try again later.')
                $('#errorModal').modal()
                $('#generate_notes').html('Generate Show Notes')
            })
          }, 500);
      })
  },

  downloadForm = function() {
    $('#download_form').on('submit',function(){
        return !formError()
    })
  },

  formError = function() {
      error = false
      $.each(cards, function(idx,card) {
          if (!card.title) {
              error = true
          }
      })

      if (error) {
        $('#errorModal .modal-body').html('Please enter a chapter title for each chapter to proceed.')
        $('#errorModal').modal()
      }
      return error;
  }

  init();
})(jQuery)