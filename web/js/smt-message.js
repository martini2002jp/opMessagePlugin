'use strict';
$(document).ready(function() {

  var message = {

    /**
     * message template.
     */
    $template: $('#message-template'),

    /**
     * config.
     */
    config: {

      /**
       * hearbeat timer.
       */
      timer: null,

      /**
       * interval (second).
       */
      interval: 5,

      /**
       * heartbeat function.
       */
      heartbeatTarget: null
    },

    /**
     * initialize.
     */
    initialize: function() {

      // set baseUrl
      if (typeof $('#baseUrl').val() == 'string') {
        openpne.baseUrl = $('#baseUrl').val();
      }
      openpne.baseUrl += '/';

      // common.
      $('.message-created-at').timeago();

      // for message/smtChain page.
      if ($('body').is('#page_message_smtChain')) {
        // message date line. - show or hide.
        this.updateTimeInfo();

        this.config.heartbeatTarget = this.addNewMessages.bind(false);

        this.addNewMessages(true).always(function() {
          // set timer.
          message.startHeartbeatTimer();
        });

        $('#do-submit').click(function() {
          message.clickDoSubmitButton();
        });

        $('#more').click(function() {
          message.clickMoreButton();
        });

        $('#message_image').change(function() {
          message.imageChangeValidator.call(this);
        });
      }

      // for message/receiveList page.
      if ($('body').is('#page_message_smtList')) {
        this.config.heartbeatTarget = this.updateNewRecentList.bind(false);

        this.updateNewRecentList(true).always(function() {
          // set timer.
          message.startHeartbeatTimer();
        });

        $('#messagePrevLink').click(function() {
          message.clickPrevButton();
        });

        $('#messageNextLink').click(function() {
          message.clickNextButton();
        });
      }
    },

    /**
     * hide and show submit fields.
     */
    submitFilter: function() {
      $('#loading').toggle();
      $('#submit-message').toggle();
      $('#do-submit').toggle();
      $('#message_image').toggle();
    },

    /**
     * hide and show more info fields.
     */
    moreFilter: function() {
      $('#loading-more').toggle();
      $('#more').toggle();
    },

    /**
     * hide more info fields.
     */
    hideMore: function() {
      $('#loading-more').hide();
      $('#more').hide();
    },

    /**
     * show more button.
     */
    showMore: function() {
      $('#loading-more').hide();
      $('#more').show();
    },

    /**
     * click #do-submit id button.
     */
    clickDoSubmitButton: function() {

      var body = $('#submit-message').val();
      if (1 > jQuery.trim(body)) {
        return;
      }

      var
        form = $('form#send-message-form'),
        formData = this.getFormData(form);

      this.submitFilter();
      this.stopHeartbeatTimer();

      $.ajax({
        url: openpne.apiBase + "message/post.json",
        type: 'POST',
        processData: false,
        contentType: false,
        data: formData,
        dataType: 'json',
      }).always(function() {

        message.addNewMessages(true).always(function() {
          message.updateTimeInfo();
          $('#no-message').hide();
          $('#submit-message').val('');
          $('#message_image').val('');
          message.submitFilter();
          message.startHeartbeatTimer();
        });
      });
    },

    /**
     * click #more id button.
     */
    clickMoreButton: function() {

      var
        firstMessageWrapper = $('.message-wrapper:first'),
        maxId = Number(firstMessageWrapper.attr('data-message-id'));

      if (isNaN(maxId)) {
        return false;
      }

      this.moreFilter();

      this.getMessages(maxId, false).done(function(response) {

        message.insertMessages(response.data, false);

        message.moreFilter();

        if (!response.has_more) {
          message.hideMore();
        }

      }).fail(function() {
        // TODO error design.
      });
    },

    /**
     * click #messagePrevLink id button.
     */
    clickPrevButton: function() {
      var prevPage = Number($('#prevPage').val());

      this.clickPagerLink(prevPage);
    },

    /**
     * click #messageNextLink id button.
     */
    clickNextButton: function() {
      var nextPage = Number($('#nextPage').val());

      this.clickPagerLink(nextPage);
    },

    clickPagerLink: function(page) {

      if (isNaN(page) || page === 0) {
        return;
      }

      this.hidePager();
      $('#message-wrapper-parent').find('.message-wrapper').remove();
      $('#messageKeyId').val(0);
      $('#memberIds').val('');

      $('#first-loading').show();
      this.stopHeartbeatTimer();

      $('#page').val(page);

      this.updateNewRecentList(true).always(function() {
        // set timer.
        message.startHeartbeatTimer();
      });
    },

    /**
     * check image file name if change image data.
     */
    imageChangeValidator: function() {

      var a = $(this).prop('files');
      if (0 < a.length) {
        var fileType = a[0].type;
        if (null === fileType.match(/(jpeg|gif|png)/)) {
          alert('ファイル形式が間違っています。');
          $(this).val('');
        }
      }
    },

    /**
     * get FromData Object. openpne apyKey and form value.
     */
    getFormData: function(form) {
      var
        formData = new FormData(form[0]);

      formData.append('apiKey', openpne.apiKey);
      formData.append('toMember', this.getMemberId());

      $(form.serializeArray()).each(function(i, v) {
        formData.append(v.name, v.value);
      });

      return formData;
    },

    /**
     * partner member id.
     */
    getMemberId: function() {
      var toMemberObj = $('#messageToMember');
      if (toMemberObj) {
        return toMemberObj.val();
      }

      return null;
    },

    /**
     * insert Message template by datas.
     * @param datas
     * @param isAddLow
     */
    insertMessages: function(datas, isAddLow) {

      if (!datas.length) {
        return false;
      }

      for (var i = 0; i < datas.length; i++) {
        this.insertMessageTemplate(datas[i], isAddLow);
      }

      this.updateTimeInfo();
    },

    /**
     * insert Message template by data.
     * @param data
     * @param isAddRow
     */
    insertMessageTemplate: function(data, isAddRow) {
      var
        template = this.$template.children().clone(),
        $photo = template.find('.photo'),
        position = data.member.id == this.getMemberId() ? 'right' : 'left';

      template
        .attr('data-message-id', data.id)
        .addClass(position)
        .addClass('show')
          .find('.time-info')
          .append(data.formatted_date)
            .parent('.time-info-wrapper')
            .attr('data-created-at-date', data.formatted_date)
          .end()
        .end()
          .find('.popover-title')
          .append(data.member.name)
        .end()
          .find('.message-body')
          .append(data.body)
        .end()
          .find('.message-created-at')
          .addClass(position)
          .attr('title', data.created_at)
          .timeago()
        .end();

      // has one image data from api. this opMessagePlugin version.
      if (data.image_path && data.image_tag) {
        $photo.append('<li><a href="' + data.image_path + '">' + data.image_tag + '</a></li>');
      } else {
        $photo.remove();
      }

      if (isAddRow) {
        $('#message-wrapper-parent').append(template);
      } else {
        $('#message-wrapper-parent').prepend(template);
      }
    },

    /**
     * update recent list message data.
     * @param datas
     */
    updateRecentListMessageTemplate: function(datas) {
      var maxId = Number($('#messageKeyId').val());

      if (isNaN(maxId)) {
        maxId = 0;
      }

      $(datas).each(function(i, data) {
        var
          template = message.$template.children().clone(),
          $oldHtml = $('div[data-member-id="' + data.member.id + '"]');

        if ($oldHtml.is('.message-wrapper')) {
          $oldHtml.remove();
        }

        if (maxId < data.id) {
          maxId = data.id;
        }

        template
          .attr('data-member-id', data.member.id)
          .addClass('show')
            .find('.memberIcon')
            .append('<a href="' + data.member.profile_url + '"><img src="' + data.member.profile_image + '" /></a>')
          .end()
            .find('.memberProfile')
            .append('<a href="' + data.member.profile_url + '">' + data.member.name + '</a>')
          .end()
            .find('.lastMessage')
            .append('<a href="' + openpne.baseUrl + 'message/smtChain?id=' + data.member.id + '">' + data.summary + '</a>')
          .end()
            .find('.message-created-at')
            .attr('title', data.created_at)
          .end();

        if (typeof data.is_read == 'boolean' && !data.is_read) {
          template.addClass('message-unread');
        }

         $('#message-wrapper-parent').prepend(template);
      });

      $('.message-created-at').timeago();
      $('#messageKeyId').val(maxId);
    },

    /**
     * update recent list pagenation.
     */
    updatePager: function(response) {

      $('#page').val(response.page);

      if (response.previousPage) {
        $('#prevPage').val(response.previousPage);
        $('#messagePrevLink').show();
      }

      if (response.nextPage) {
        $('#nextPage').val(response.nextPage);
        $('#messageNextLink').show();
      }

      if (response.previousPage || response.nextPage) {
        $('.pager').show();
      }
    },

    /**
     * hide pagenation.
     */
    hidePager: function() {
      $('#messagePrevLink').hide();
      $('#messageNextLink').hide();
      $('.pager').hide();

      $('#nextPage').val('');
      $('#prevPage').val('');
      $('#page').val('');
    },

    /**
     * update Time info line.
     */
    updateTimeInfo: function() {
      var
        timeInfoWrapper = $('#message-wrapper-parent').find('.time-info-wrapper'),
        currentDate,
        baseDate;

      for (var i = 0; i < timeInfoWrapper.length; i++) {
        currentDate = timeInfoWrapper.eq(i).attr('data-created-at-date');
        if (currentDate) {
          if (currentDate === baseDate) {
            timeInfoWrapper.eq(i).hide();
          } else {
            timeInfoWrapper.eq(i).show();
          }

          baseDate = currentDate;
        }
      }
    },

    /**
     * start heartbeat timer.
     */
    startHeartbeatTimer: function() {
      this.config.timer = setTimeout(this.config.heartbeatTarget, this.config.interval * 1000);
    },

    /**
     * stop heartbeat timer.
     */
    stopHeartbeatTimer: function() {
      clearTimeout(this.config.timer);
    },

    /**
     * insert Message template by data.
     * @param keyId
     * @param isAddLow
     */
    getMessages: function(keyId, isAddLow) {

      var dfd = $.Deferred();

      $.ajax({
        url: openpne.apiBase + "message/search.json",
        type: 'POST',
        data: {
          apiKey: openpne.apiKey,
          memberId: Number(this.getMemberId()),
          maxId: Number(keyId),
          isAddLow: Number(isAddLow)
        },
        dataType: 'json',
        success: function(response) {
          dfd.resolve(response);
        },
        error: function(e) {
          dfd.reject();
        }
      });

      return dfd.promise();
    },

    /**
     * insert Message template by data.
     * @param keyId
     */
    getRecentList: function(keyId, page, memberIds) {

      var dfd = $.Deferred();

      $.ajax({
        url: openpne.apiBase + "message/recentList.json",
        type: 'POST',
        data: {
          apiKey: openpne.apiKey,
          keyId: Number(keyId),
          page: Number(page),
          memberIds: memberIds,
        },
        dataType: 'json',
        success: function(response) {
          dfd.resolve(response);
        },
        error: function(e) {
          dfd.reject();
        }
      });

      return dfd.promise();
    },

    /**
     * add new messages.
     */
    addNewMessages: function(notUseHeartbeat) {

      var
        lastMessageWrapper = $('#message-wrapper-parent').find('.message-wrapper:last'),
        minId = Number(lastMessageWrapper.attr('data-message-id')),
        dfd = $.Deferred();

      if (isNaN(minId)) {
        minId = -1;
      }

      message.getMessages(minId, true).done(function(response) {

        $('#first-loading').hide();
        message.insertMessages(response.data, true);
        dfd.resolve();

        if (!$('#message-wrapper-parent').find('.message-wrapper').length) {
          message.hideMore();
          $('#no-message').show();

          return false;
        }

        $('#no-message').hide();

        if (minId == -1 && response.has_more) {
          message.showMore();
        }

      }).always(function() {

        if (!notUseHeartbeat) {
          message.startHeartbeatTimer();
        }

      }).fail(function() {
        dfd.reject();
        // TODO error design.
      });

      return dfd.promise();
    },

    /**
     * update new recent list.
     */
    updateNewRecentList: function(notUseHeartbeat) {

      var
        keyId = Number($('#messageKeyId').val()),
        page = Number($('#page').val()),
        memberIds = $('#memberIds').val(),
        dfd = $.Deferred();

      if (isNaN(keyId)) {
        keyId = 0;
      }

      if (isNaN(page)) {
        page = 1;
      }

      message.getRecentList(keyId, page, memberIds).done(function(response) {

        $('#first-loading').hide();

        if (response.memberIds.length) {
          $('#memberIds').val(response.memberIds);
        }

        message.updatePager(response);
        message.updateRecentListMessageTemplate(response.data);
        dfd.resolve();

        if (!$('#message-wrapper-parent').find('.message-wrapper').length) {
          $('#no-message').show();

          return false;
        }

        $('#no-message').hide();

      }).always(function() {

        if (!notUseHeartbeat) {
          message.startHeartbeatTimer();
        }

      }).fail(function() {
        dfd.reject();
        // TODO error design.
      });

      return dfd.promise();
    }
  };

  message.initialize();
});
