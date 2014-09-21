# Examples

## 1. Download file

    <?php
    namespace vestibulum;
    download(__DIR__ . '/example.txt', 'download.txt');

[See download example](download)

## 2. Send email message

Have basic HTML file (e.g. `contact.html`)

    <h1>Contact form</h1>

    <form id="contactForm" action="%url%/examples/email/send" class="form-horizontal" style="margin: 60px 0" method="post">

      <div class="form-group">
        <label for="subject" class="col-sm-2">Subject</label>

        <div class="col-sm-10">
          <input type="text" id="subject" name="subject" placeholder="Subject of message" class="form-control"/>
        </div>
      </div>
      <div class="form-group">
        <label for="from" class="col-sm-2">Email</label>

        <div class="col-sm-10">
          <input type="email" id="from" name="from" placeholder="Your email address" class="form-control"/>
        </div>
      </div>
      <div class="form-group">
        <label for="message" class="col-sm-2">Message</label>

        <div class="col-sm-10">
          <textarea type="email" id="message" name="message" placeholder="Your message" rows="10" class="form-control"
                    required="required"></textarea>
        </div>
      </div>

      <!-- Antispam protection -->
      <script type="text/javascript">/* <![CDATA[ */
      document.write('<input type="hidden" name="captcha" id="captcha" value="P' + 'r' + 'a' + 'g' + 'u' + 'e" />');
      /* ]]> */
      </script>

      <noscript>
        <div class="form-group">
          <label for="captcha" class="col-sm-2">Write "<em>Prague</em>"</label>

          <div class="col-sm-10">
            <input type="text" name="captcha" id="captcha" value="" class="form-control" placeholder="Are you human?"/>
          </div>
        </div>
      </noscript>
      <!-- /Antispam protection -->


      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <div id="alerts"></div>
          <button type="submit" class="btn btn-default">Send email</button>
        </div>
      </div>

    </form>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>

    <script>
      (function ($) {
        var $mesage = $('#message');
        var $alerts = $('#alerts');
        var $subject = $('#subject');
        var $form = $('#contactForm');

        return $form.submit(function (e) {
          $alerts.html('');

          if ($mesage.val() === '') {
            $alerts.append('<p class="alert alert-danger">Please write something</p>');
          } else {
            $mesage.addClass('loading');
            $.post($form.attr('action'), $form.serialize(), (function (response) {
              if (response.error) {
                return $alerts.append('<p class="alert alert-danger">' + response.message + '</p>');
              } else {
                $alerts.append('<p class="alert alert-success">' + response.message + '</p>');
                $mesage.val('');
                $subject.val('');
              }
            }), 'json');
            $mesage.removeClass('loading');
          }
          return e.preventDefault();
        });
      })(jQuery);
    </script>

And simple PHP file `send.php`

    <?php
    namespace vestibulum;

    isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');

    // check AJAX request
    isAjax() or json(['message' => 'Not AJAX request, but nice try :-)']);

    // captcha check
    $captcha = isset($_POST['captcha']) ? $_POST['captcha'] : null;
    $captcha === 'Prague' or json(['error' => true, 'message' => 'Captcha failed! Please write Prague']);

    // send email
    $from = isset($_POST['from']) ? $_POST['from'] : 'nobody@nobody';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : null;
    $message = isset($_POST['subject']) ? $_POST['subject'] : null;

    if (mail('ozana@omdesign.cz', $subject, $message, "From: $from\n")) {
      json(['message' => 'Well done! Your message was send!', 'error' => false]);
    } else {
      json(['message' => 'Something went wront :-(', 'error' => true]);
    }

[See email example](email)