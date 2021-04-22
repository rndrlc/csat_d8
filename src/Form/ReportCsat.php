<?php

namespace Drupal\csat\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extends FormBase.
 *
 * @inheritdoc
 */
class ReportCsat extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'csat_report_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $values        = $form_state->getValues();
    $date_from_set =  (isset($values['date_from'])) ? $values['date_from'] : date('d-M-Y');
    $date_to_set   =  (isset($values['date_to'])) ? $values['date_to'] : date('d-M-Y');
    
    $date_from     = 0;
    $date_to       = 0;

    $form['csat_report'] = array(
      '#type'        => 'container',
      '#title'       => t('CSAT Report'),
      '#description' => t('CSAT Report.'),
    );

    if(!empty($date_from_set))
    {
      $date_from_c = date('m/d/Y 00:00:00', strtotime($date_from_set));
      // $date_from = strtotime($date_from_set);
      $date_from = strtotime($date_from_c);
    }

    if(!empty($date_to_set))
    {
      $date_to_c = date('m/d/Y 23:59:59', strtotime($date_to_set));
      // $date_to = strtotime($date_to_set);
      $date_to = strtotime($date_to_c);
    }

    if($date_from > 0 && $date_to > 0)
    {
      $result = db_query("SELECT pid, comment, rating, score, created_at FROM csat WHERE created_at >= {$date_from} AND created_at <= {$date_to} ORDER BY created_at DESC");
    }
    else
    {
      $result = db_query('SELECT pid, comment, rating, score, created_at FROM csat  ORDER BY created_at DESC');
    }

    $rows      = '';
    $counter   = 0;
    $pos_score = 0;
    $average   = 0;
    if($result)
    {
      foreach ($result as $row) 
      {
        $counter    = $counter + 1;
        if((int)$row->score > 3) $pos_score  = $pos_score + 1;
        $rows .= '<tr>
                    <td class="text-center" width="10%">'.$counter.'</td>
                    <td class="text-center" width="20%">'.$this->csat_rating($row->rating).'</td>
                    <td class="text-center" width="20%">'.$row->score.'</td>
                    <td width="30%">'.$row->comment.'</td>
                    <td width="20%">'.date('d-M-Y H:i', $row->created_at).'</td>
                  </tr>';
      }

    }
    if($counter > 0) $average   = $pos_score / $counter;
    $form['csat_report']['table'] = [
      '#markup' =>  '<table class="table display responsive dt-responsive" id="reports_table" width="100%" style="width: 100% !important;">
                      <thead><tr>
                        <th class="text-center"></th>
                        <th class="text-center">Rate</th>
                        <th class="text-center">Score</th>
                        <th>Comment</th>
                        <th>Date Rated</th>
                      </tr></thead>
                      <tbody>'.$rows.'</tbody></table>

                      <h2>CSAT: '.number_format ($average, 2).' * 100 = '.number_format ( ($average * 100), 2 ).'%</h2>',
    ];

    $form['csat_report']['date_from'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Date From'),
      '#default_value' => $date_from_set,
      '#size'          => 60,
      '#maxlength'     => 250,
      '#attributes'    => array('class' => array('datepicker')),
      // '#required'   => TRUE,
    );

    $form['csat_report']['date_to'] = array(
      '#type'           => 'textfield',
      '#title'          => t('Date To'),
      '#default_value'  => $date_to_set,
      '#maxlength'      => 250,
      '#attributes'     => array('class' => array('datepicker')),
      // '#description' => t("Something"),
      // '#required'    => TRUE,
    );

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit', 
      '#value' => t('Apply'), 
      // '#submit' => array('csat_report_submit')

    ];
    $form['csat_report']['#attached'] = [
      'library' => ['csat/csat-report'],
    ];
    
    return $form;
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

  /**
   * FormInterface submitForm method must be declared.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRebuild(TRUE);
    \Drupal::service('cache.render')->invalidateAll();
  }


}
