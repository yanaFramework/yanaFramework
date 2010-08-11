<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>{lang id="PROGRAM_TITLE"}</title>
        <link rel="stylesheet" type="text/css" href="../styles/default.css"/>
    </head>

<body>
    <form method="post" enctype="multipart/form-data" action="{$PHP_SELF}" class="guestbook_form" id="guestbook_comment{$TARGET}" onsubmit="YanaGuestbook.prototype.guestbookRequest('{$ACTION_COMMENT_WRITE}','guestbook_comment{$TARGET}','target={$TARGET}&guestbook_comment='+document.getElementById('input_GUESTBOOK_COMMENT').value); return false">
      <input type="hidden" name="id" value="{$ID}"/>
      <input type="hidden" name="action" value="{$ACTION_COMMENT_WRITE}"/>
      <input type="hidden" name="{$SESSION_NAME}" value="{$SESSION_ID}"/>
      <input type="hidden" name="target" value="{$TARGET}"/>

      <div class="label">
        {lang id="8"}:<br />
        <textarea title="{lang id="14"}" rows="5" id="input_GUESTBOOK_COMMENT" name="guestbook_comment" cols="40">{$GUESTBOOK_COMMENT}</textarea>
      </div>

      <!-- BEGIN: Embedded Tags -->
      <div class="label">{lang id="FORMAT_TEXT"}:<br />
      {embeddedTags show="b,i,u,url,mark,color,smilies"}
      <!-- END: Embedded Tags -->
      </div>

      <p>
          <input type="submit" value="{lang id="BUTTON_SAVE"}"/>
      </p>
    </form>
</body>

</html>
