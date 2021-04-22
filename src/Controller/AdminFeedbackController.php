<?php

namespace Drupal\csat\Controller;

use Drupal\csat\Form\CsatClass;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Datetime\DrupalDateTime;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for csat routes.
 */
class AdminFeedbackController extends ControllerBase {

  /**
   * Csat Admin Page.
   *
   * @param string $type
   *   The type of the overview form ('open' or 'resolved').
   *
   * @return array
   *   Returns renderable array that display all content feedbacks.
   */
  public function content($type = 'open') {
    $header = [
      'pid' => '',
      'rating' => $this->t('Rate'),
      'score' => $this->t('Score'),
      'comment' => $this->t('Comment'),
      'created_at' => $this->t('Date Rated'),
    ];

    $rows = [];

    $feedbacksList = CsatClass::getAllCsat($header, $type);
    // if ($feedbacksList) {
      foreach ($feedbacksList as $content) {
        $submitDate = DrupalDateTime::createFromTimestamp($content->created_at,
          new \DateTimeZone(date_default_timezone_get())
        );
        $rows[] = [
          'data' => [
            $content->pid,
            $content->rating,
            $content->score,
            $content->comment,
            $submitDate->format('d-M-Y H:i'),
          ],
        ];
      }

      // Generate the table.
      $build['config_table'] = [
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#attributes' => [
          'id' => 'fb-table reports_table', 
          'class' => 'table display responsive dt-responsive',
        ],
      ];

      $build['csat']['date_from'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Date From'),
        '#default_value'  => date('d-M-Y'),
        '#size'          => 60,
        '#maxlength'     => 250,
        '#attributes'    => array('class' => array('datepicker')),
        // '#required'   => TRUE,
      );

      $build['csat']['date_to'] = array(
        '#type'           => 'textfield',
        '#title'          => t('Date To'),
        '#default_value'  => date('d-M-Y'),
        '#maxlength'      => 250,
        '#attributes'     => array('class' => array('datepicker')),
        // '#description' => t("Something"),
        // '#required'    => TRUE,
      );


      
    $build['actions']['#type'] = 'actions';
    $build['actions']['submit'] = array('#type' => 'submit', '#value' => t('Apply'), '#submit' => array('csat_report_submit'));


    return $build;
  }


  public function csat_submit()
  {
    
    // Retrieve values
    $rate      = $_POST['rate'];
    $feedback  = $_POST['feedback'];

    $fields    = array(
      // 'first_name'   => $form_state->getValue('first_name'),
      // 'last_name'    => $form_state->getValue('last_name'),
      // 'email'        => $form_state->getValue('email'),
      'comment'         => $feedback,
      'rating'          => $this->csat_rating($rate),
      'score'           => $rate,
      // 'data_privacy' => $form_state->getValue('data_privacy'),
      // 'locations'    => $form_state->getValue('location_name'),
      // 'release_id'   => $form_state->getValue('release_id'),
      // 'department'   => trim($department),
      // 'division'     => trim($division),
      // 'followup'     => $form_state->getValue('followup'),
      'ip_address'      => \Drupal::request()->getClientIp(),
      'created_at'      => strtotime('now')
    );

    db_insert('csat')->fields($fields)->execute();
    
    return new JsonResponse(array('status' => 0, 'data' => $fields));
  }

  function csat_rating($score)
  {
    $rate = $score;
    switch ($rate) {
      case '1':
        $rate = 'Terrible';
        break;
      case '2':
        $rate = 'Bad';
        break;
      case '3':
        $rate = 'Okay';
        break;
      case '4':
        $rate = 'Good';
        break;
      case '5':
        $rate = 'Excellent';
        break;
      
      default:
        # code...
        break;
    }

    return $rate;
  }
}
