(function($) {
/* "use strict" */

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });




    $("#search_name").on('click', function(){
        $(this).prop('disabled','disabled');
        let keyword = $("#feed_name").val();
        if (keyword.length > 3){
            let loading = $('<div class="spinner-border"></div>');
            $('#feed_result').html(loading);
            $.ajax({
                url: '/search-podcast',
                type: 'POST',
                data: {'keyword': keyword},
            }).done(function(data) {
                $("#search_name").prop('disabled',false);
                updateSearchResult(data);
            })
        }
    });

    $("#search_url").on('click', function(){
        $(this).prop('disabled','disabled');
        let url = $("#feed_url").val();
        if (url){
            let loading = $('<div class="spinner-border"></div>');
            $('#feed_result').html(loading);
            $.ajax({
                url: '/search-podcast',
                type: 'POST',
                data: {'url': url},
            }).done(function(data) {
                $("#search_url").prop('disabled',false);
                updateSearchResult(data);
            })
        }
    });

    let updateSearchResult = function(data) {
        $('#feed_result').html('');
        if (data.feed) {
            data.feeds = [data.feed];
        }
        $.each(data.feeds, function(index, element) {
            let podcast = $('<div class="row feed-item mb-4"></div>');
            let left = $('<div class="col-sm-2"></div>')
            let center = $('<div class="col-sm-9"></div>')
            let right = $('<div class="col-sm-1 text-right"></div>')
            let image = $('<img class="artwork img-fluid">').attr('src', element.image);
            let title = $('<h4 class="title"></h4>').html(element.title);
            let description = $('<div class="description"></div>').text(element.description);
            let url = $('<div class="url d-none"></div>').text(element.url);
            let btnAdd = $('<button class="btn btn-primary btn-sm">Add</button>').on('click', function() {
                let feedUrl = $(this).closest('.feed-item').find('.url').text();
                $(this).html('<div class="spinner-border spinner-border-sm"></div>');
                addShow(element.url, $(this));
            });
            if (added_podcasts.indexOf(element.url) > -1){
                btnAdd = $('<button class="btn btn-primary btn-sm" disabled>Added</button>')
            }
            left.append(image);
            center.append(title);
            center.append(description);
            center.append(url);
            right.append(btnAdd);
            podcast.append(left);
            podcast.append(center);
            podcast.append(right);
            $('#feed_result').append(podcast);
        });

        if (!data.feeds.length) {
            $('#feed_result').html('<p>No results found.</p>');
        }
    }

    let addShow = function(feed_url, btn) {
        $('#addForm input[name=feed_url]').val(feed_url);
        $('#addForm').trigger('submit');
    }

    $('#addPodcastModal').on('hidden.bs.modal', function (e) {
      $('#feed_name').val('');
      $('#feed_url').val('');
      $('#feed_result').html('');
    })

})(jQuery)