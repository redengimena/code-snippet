(function($) {
/* "use strict" */

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var ids = [];
    $('.dd-item').each(function(){
        ids.push({'id': $(this).data('id') });
    });

    $('.dd').nestable({ /* config options */ });
    $('.dd').on('change', function() {
        let new_ids = $('.dd').nestable('serialize');
        if (JSON.stringify(new_ids) != JSON.stringify(ids)) {
            //update order
            $.ajax({
                url: '/admin/top-shows/order',
                type: 'POST',
                data: {'data': new_ids},
            }).done(function(json) {
                d = JSON.parse(json)
                if (!d.success) {
                  alert('Error updating order');
                } else {
                  ids = new_ids;
                }
            });
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

    $('.top-show-list').on('click', '.btn-primary', function(){
        $(this).html('<div class="spinner-border spinner-border-sm"></div>');
        deleteTopShow($(this).data('id'), $(this));
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
        btn.prop('disabled',true);
        $.ajax({
            url: '/admin/top-shows/add',
            type: 'POST',
            data: {'feed_url': feed_url},
        }).done(function(json) {
            btn.prop('disabled', false);

            d = JSON.parse(json)
            if (d.success) {
                $('#addTopShowModal').modal('hide');
                let item = $('<li class="dd-item">');
                let row = $('<div class="row p-3">');
                let left = $('<div class="col-sm-1">');
                let middle = $('<div class="col-sm-9">');
                let right = $('<div class="col-sm-2 text-right">');
                let a = $('<a>').attr('href', '/podcasts/'+d.data.id+'/episodes');
                let button = $('<button class="btn btn-primary btn-xxs">').text('Delete').data('id', d.data.id);
                item.data('id', d.data.id);
                left.append($('<img class="img-fluid dd-handle">').attr('src', d.data.image));
                middle.append(a.append($('<h4>').text(d.data.title)));
                middle.append($('<div>').text(d.data.description));
                right.append(button);
                row.append(left).append(middle).append(right);
                item.append(row);
                $('.top-show-list ol.dd-list').append(item);
                added_podcasts.push(d.data.feed_url);
                ids = $('.dd').nestable('serialize');
            }
        })
    }

    let deleteTopShow = function(id, btn) {
        $.ajax({
            url: '/admin/top-shows/delete',
            type: 'POST',
            data: {'id': id},
        }).done(function(json) {
            btn.prop('disabled', false);

            d = JSON.parse(json)
            if (d.success) {
                let index = added_podcasts.indexOf(d.data);
                added_podcasts.splice(index,1);
                ids = $('.dd').nestable('serialize');
                btn.closest('.dd-item').remove();

            } else {
                btn.text('Delete');
            }
        })
    }

    $('#addTopShowModal').on('hidden.bs.modal', function (e) {
      $('#feed_name').val('');
      $('#feed_url').val('');
      $('#feed_result').html('');
    })

})(jQuery)