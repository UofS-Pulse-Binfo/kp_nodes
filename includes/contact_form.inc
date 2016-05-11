<?php
/**
 * Provides a site-wide contact form for KnowPulse.
 */

/**
 * A KnowPulse specific contact form.
 */
function kp_contact_form($form, $form_state) {

  // Include CSS
  $path = drupal_get_path('module','kp_nodes');
  $form['#attached']['css'][] = $path . '/css/contact_us.css';

  // Determine the email of the user if logged in to be used as a default.
  global $user;
  // Luckily the email is part of the core user so we can just access it directly.
  $email = '';
  if ($user AND isset($user->mail)) {
    $email = $user->mail;
  }
  // The full name is not, however, so we have to fully load the user first.
  $full_user = user_load($user->uid);
  $name = '';
  if ($full_user AND !empty($full_user->field_full_name)) {
    $name = $full_user->field_full_name['und'][0]['safe_value'];
  }

  $form['c'] = array(
    '#type' => 'fieldset',
    '#title' => 'Contact us',
  );

  $form['c']['f'] = array(
    '#type' => 'markup',
    '#prefix' => '<span class="contact-wrapper"><span class="form left">',
    '#suffix' => '</span>',
  );

  $form['c']['f']['to'] = array(
    '#type' => 'markup',
    '#prefix' => '<div class="field-title">To</div>'
                    .'<div class="contact-section">',
    '#suffix' => '</div>',
  );

  $form['c']['f']['to']['name'] = array(
    '#type' => 'item',
    '#markup' => 'The KnowPulse Team',
  );

  $form['c']['f']['from'] = array(
    '#type' => 'markup',
    '#prefix' => '<div class="field-title">From</div>'
                    .'<div class="contact-section">',
    '#suffix' => '</div>',
  );

  $form['c']['f']['from']['name'] = array(
    '#type' => 'textfield',
    '#title' => 'Name',
    '#description' => 'We would like to know your name so we can address you more personally.',
    '#default_value' => $name,
    '#attributes' => array(
        'placeholder' => t('Your Name'),
    ),
  );

  $form['c']['f']['from']['email'] = array(
    '#type' => 'textfield',
    '#title' => 'Email',
    '#description' => 'Please enter the email address you would like us to contact you at.',
    '#default_value' => $email,
    '#required' => TRUE,
    '#attributes' => array(
        'placeholder' => t('Your Email'),
    ),
  );

  $form['c']['f']['msg'] = array(
    '#type' => 'textarea',
    '#rows' => 10,
    '#title' => 'Message',
    '#required' => TRUE
  );

  $form['c']['f']['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Send',
  );

  $form['c']['i'] = array(
    '#type' => 'markup',
    '#prefix' => '<span class="info right">',
    '#suffix' => '</span></span>',
  );

  $form['c']['i']['drawing'] = array(
    '#type' => 'markup',
    '#prefix' => '<span id="map">',
    '#suffix' => '</span>',
    '#markup' => theme('kp_worldmap'),
  );

  $form['c']['i']['paragraph'] = array(
    '#type' => 'item',
    '#markup' => 'KnowPulse is developed by the Pulse Crop Bioinformatics group at the University of Saskatchewan. If you have run into any problems with KnowPulse, we would love to hear about them as we are constantly striving to improve our portal. In the same vein, comments, suggestions for improvement and/or feature requests would be very much appreciated!'
      . '<br /><div class="signature">~ The KnowPulse Team</div>',
  );

  return $form;
}

/**
 * Validate the contact form.
 */
function kp_contact_form_validate($form, $form_state) {

  // Make sure it is a valid email address.
  if (!valid_email_address($form_state['values']['email'])) {
    form_set_error('email', 'Please provide a valid email address -I would really like to be able to respond to your message!');
  }
}

/**
 * Submit the contact form (ie: send the email).
 */
function kp_contact_form_submit($form, $form_state) {
  global $base_url;
  $site_name = variable_get('site_name');
  $admin_user = user_load(1);

  // Set who this email is being sent from (ie: from KnowPulse)
  $from = 'admin@knowpulse.usask.ca';

  // The email of the user trying to contact us.
  $valid_email = $form_state['values']['email'];

  // Compile the body of the email to send.
  $body[] = 'Name: ' . $form_state['values']['name'];
  $body[] = 'Email: '.$valid_email;
  $body[] = 'URL: '.request_uri() . "\n";
  $body[] = $form_state['values']['msg'];

  // Who to send the email to.
  $to = $admin_user->mail;

  $params = array(
    'body' => $body,
    'subject' => 'Website Information Request',
  );
  if (drupal_mail('kp_contact', 'kp_contact', $to, language_default(), $params, $from, TRUE)) {
    drupal_set_message('Thank you for taking the time to contact us! We will get back to you as soon as possible.');
  }
  else {
    drupal_set_message('Our system encountered an error trying to contact us. Please check your email address and try again.', 'error');
  }

}

/**
 * Implements hook_mail().
 * Used for the the drupal mail system.
 */
function kp_contact_mail($key, &$message, $params) {

  $headers = array(
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/plain; charset=UTF-8;',
    'Content-Transfer-Encoding' => '8Bit',
    'X-Mailer' => 'Drupal'
  );

  foreach ($headers as $key => $value) {
    $message['headers'][$key] = $value;
  }

  $message['subject'] = $params['subject'];
  $message['body'] = $params['body'];
}
