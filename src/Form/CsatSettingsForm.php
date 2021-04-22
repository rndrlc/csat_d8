<?php

namespace Drupal\csat\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Content feedback settings form.
 */
class CsatSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csat_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['csat.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('csat.settings');

    $form['global'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => $config->get('global'),
    ];

    $form['align'] = array(
      '#type'        => 'select',
      '#title'       => $this->t('Alignment'),
      '#description' => $this->t('Side of the window to attach to.'),
      '#options'     => array(
      'left'         => $this->t('Left'),
      'right'        => $this->t('Right'),
      ),
      '#default_value' =>  !empty($config->get('align')) ? $config->get('align') : $this->t('right'),
    );

    for ($i = 0; $i <= 100; $i += 5) {
      $top["$i%"] = "$i%";
    }
    $form['top'] = array(
      '#type'          => 'select',
      '#title'         => $this->t('Top'),
      '#options'       => $top,
      '#description'   => $this->t('Distance from the top.'),
      '#default_value' =>  !empty($config->get('top')) ? $config->get('top') : $this->t('75%'),
    );
    // $form['top']['#options'] = $top;

    $form['image'] = array(
      '#type'          => 'hidden',//'textfield',
      '#title'         => $this->t('Image'),
      '#description'   => $this->t('Path to the image.'),
      '#default_value' => !empty($config->get('image')) ? $config->get('image') : \Drupal::request()->getSchemeAndHttpHost(). '/' .drupal_get_path('module', 'csat') . '/csat.png',
    );

    $form['alt'] = array(
      '#type'          => 'hidden',//'textfield',
      '#title'         => $this->t('Image alt'),
      '#description'   => $this->t('Alternative text.'),
      '#default_value' => !empty($config->get('alt')) ? $config->get('alt') : 'CSAT',
    );

    $form['autoopen'] = array(
      '#type'          => 'checkbox',
      '#title'         => $this->t('Enabled Auto Open Survey On Page Load'),
      // '#description'   => $this->t('Enabling this feature will disable the <i>Minutes</i> trigger.'),
      '#default_value' => !empty($config->get('autoopen')) ? $config->get('autoopen') : FALSE,
    );
    $form['form_autoopen_markup'] = array(
      '#markup' => $this->t('<div class="csat-description">Enabling this feature will disable the <i>Minutes</i> trigger.</div>'),
    );

    $form['minutes'] = array(
      '#type'          => 'select',
      '#title'         => $this->t('Minutes'),
      // '#description'   => $this->t('Triggers how many minutes to automatically open CSAT.'),
      '#default_value' => !empty($config->get('minutes')) ? $config->get('minutes') : '0',
    );
    $form['form_minutes_markup'] = array(
      '#markup' => $this->t('<div class="csat-description">Triggers how many minutes to automatically open CSAT.</div>'),
    );
    for ($i = 0; $i <= 60; $i += 1) {
      $minutes["$i"] = "$i";
    }
    $form['minutes']['#options'] = $minutes;

    // Get list of content types.
    $node_type = node_type_get_names();
    $form['content_types'] = [
      '#type' => 'hidden',//'checkboxes',
      // '#options' => $node_type,
      '#title' => $this->t('List of content types on which you want csat.'),
      '#description' => $this->t('Select Content type for which you want to display the csat.'),
      // '#default_value' => is_array($config->get('content_types')) ? array_filter($config->get('content_types')) : [NULL],
      // '#states' => [
      //   'visible' => [
      //     ':input[name="global"]' => ['checked' => FALSE],
      //   ],
      // ],
    ];

    $form['feedback_form'] = [
      '#type' => 'hidden',//'item',
      '#title' => $this->t('Dialog Settings'),
    ];


    $form['dialog_size'] = [
      '#type'    => 'hidden',//'checkbox',
      '#title'   => $this->t('Auto Resize'),
      '#default_value' => $config->get('dialog_size'),
    ];

    $form['dialog_width'] = [
      '#type'    => 'hidden',//'number',
      '#title'   => $this->t('Width'),
      '#description' => $this->t('Width in %.'),
      // '#min' => 0,
      // '#max' => 100,
      // '#default_value' => !empty($config->get('dialog_width')) ? $config->get('dialog_width') : 40,
      // '#states' => [
      //   'visible' => [
      //     ':input[name="dialog_size"]' => ['checked' => FALSE],
      //   ],
      // ],
    ];

    $form['name'] = [
      '#type' => 'hidden',//'checkboxes',
      '#title' => $this->t('Name'),
      // '#options' => ['show' => $this->t('Show'), 'required' => $this->t('Required')],
      // '#default_value' => !empty($config->get('name')) ? $config->get('name') : ['show'],
    ];

    $form['email'] = [
      '#type' => 'hidden',//'checkboxes',
      '#title' => $this->t('Email'),
      // '#options' => ['show' => $this->t('Show'), 'required' => $this->t('Required')],
      // '#default_value' => !empty($config->get('email')) ? $config->get('email') : ['show'],
    ];

    $form['form_denyallow_markup'] = array(
      '#markup' => $this->t('<h3>Visibility rules</h3><p>By default, the Feedback tab
        shows on every page except on the link/s set below. Paths can explicity be
        set to hide or show below, by listing them with wild cards, one per line.</p>'),
    );

    $form['disable'] = [
      "#type" => 'textarea',
      '#title' => $this->t('Deny'),
      // '#description' => $this->t('Hide on these paths. Asterisk * can be used for wildcards. (Ex. /admin*)'),//('Specify paths for which csat should be disabled by listing them one per line. Ex: "/about"'),
      '#default_value' => !empty($config->get('disable')) ? $config->get('disable') : '',
    ];
    $form['form_deny_markup'] = array(
      '#markup' => $this->t('<div class="csat-description">Hide on these paths. Asterisk * can be used for wildcards. (Ex. /admin*)</div>'),
    );


    // $form['deny'] = array(
    //   "#type"          => 'textarea',
    //   '#title'         => t('Deny'),
    //   '#description'   => t('Hide on these paths. Asterisk * can be used for wildcards. (Ex. admin/reports)'),
    //   '#default_value' => !empty($config->get('deny')) ? $config->get('deny') : 'admin*',
    // );
    $form['allow'] = array(
      "#type"          => 'textarea',
      '#title'         => $this->t('Allow'),
      // '#description'   => $this->t('Show on these paths. Asterisk * can be used for wildcards. (Ex. /admin/reports)'),
      '#default_value' => !empty($config->get('allow')) ? $config->get('allow') : '',
    );
    $form['form_allow_markup'] = array(
      '#markup' => $this->t('<div class="csat-description">Show on these paths. Asterisk * can be used for wildcards. (Ex. /admin/config/content/csat_report)</div>'),
    );

    $form['height'] = array(
      "#type"          => 'hidden',
      '#title'         => $this->t('Icon Height'),
      '#description'   => $this->t('Unit of measure in pixels (px)'),
      '#default_value' => !empty($config->get('height')) ? $config->get('height') : '50',
    );

    $form['width'] = array(
      "#type"          => 'hidden',
      '#title'         => $this->t('Icon Weight'),
      '#description'   => $this->t('Unit of measure in pixels (px)'),
      '#default_value' => !empty($config->get('width')) ? $config->get('width') : '50',
    );

    $form['bgcolor'] = array(
      "#type"          => 'textfield',
      '#title'         => $this->t('Background Color'),
      // '#description'   => $this->t('Ex. Hex color codes (#0000000), black, blue, red, green, yellow, etc.'),
      '#default_value' => !empty($config->get('bgcolor')) ? $config->get('bgcolor') : 'white',
    );
    $form['form_bgcolor_markup'] = array(
      '#markup' => $this->t('<div class="csat-description">Ex. Hex color codes (#0000000), black, blue, red, green, yellow, etc.</div>'),
    );

    $form['question'] = array(
      "#type"          => 'textarea',
      '#title'         => $this->t('Survey Question'),
      '#default_value' => !empty($config->get('question')) ? $config->get('question') : 'How was your overall experience while using the application?',
    );

    $form['message'] = array(
      "#type"          => 'textarea',
      '#title'         => $this->t('Success Message'),
      '#default_value' => !empty($config->get('message')) ? $config->get('message') : 'Thanks for your rating!',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('csat.settings');
    $config->set('global', $form_state->getValue('global'));
    $config->set('dialog_size', $form_state->getValue('dialog_size'));
    $config->set('dialog_width', $form_state->getValue('dialog_width'));
    $config->set('content_types', $form_state->getValue('content_types'));
    $config->set('name', $form_state->getValue('name'));
    $config->set('email', $form_state->getValue('email'));
    $config->set('disable', $form_state->getValue('disable'));


    $config->set('align', $form_state->getValue('align'));
    $config->set('top', $form_state->getValue('top'));
    $config->set('image', $form_state->getValue('image'));
    $config->set('alt', $form_state->getValue('alt'));
    $config->set('autoopen', $form_state->getValue('autoopen'));
    $config->set('minutes', $form_state->getValue('minutes'));
    $config->set('allow', $form_state->getValue('allow'));
    $config->set('height', $form_state->getValue('height'));
    $config->set('width', $form_state->getValue('width'));
    $config->set('bgcolor', $form_state->getValue('bgcolor'));
    $config->set('question', $form_state->getValue('question'));
    $config->set('message', $form_state->getValue('message'));
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
