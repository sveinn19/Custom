<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
* Form for searching music.
 */

 class CreateContentForm extends FormBase{
    public function getFormId(){
        return 'create_content_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $result = $_SESSION['s1'][$_SESSION['s2'] . 's']['items'];//[$_SESSION['con']];
        $_SESSION['result'] = $result;

        $this->get_inputs();

        $form['text'] = array(
            '#type' => 'checkboxes',
            '#title' => 'Choose what to create',
            '#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
            '#prefix' => "<h2>Discogs</h2><pre>". print_r($_SESSION['dc-res'], true)."</pre>",
            //'#suffix' => "<pre>".print_r($result, true)."</pre>",
        );

        $form['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Submit'),

        );

        return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){
        \Drupal::messenger()->addMessage(t('CreateContent'));

        $result = $_SESSION['spot-res'][0];
        $url = substr($result['external_urls']['spotify'], 8, strlen($result['external_urls']['spotify']));

        $node = Node::create([
            'type'  => 'listamadur',
            'title' =>  $result['name'],
          ]);
          $node->field_nafn->value = $result['name'];
          $node->set('field_vefsida_listamanns', [
            'uri' => $result['external_urls']['spotify'],
            'title' => $result['external_urls']['spotify'],
            'options' => [],
          ]);

          $node->save();
    }

    private function get_inputs(){
        $temp_sp = [];
        $temp_dc = [];

        foreach($_SESSION['con'] as $key => $value){
            if ($value !== 0){
                //$temp[$key] = [substr($value, -2), substr($value, 0, -2)];
                if (substr($value, -2) == 'sp'){
                    array_push($temp_sp, $_SESSION['s1'][$_SESSION['s2'] . 's']['items'][(int)substr($value, 0, -2)]);
                }
                else {
                    array_push($temp_dc, $_SESSION['d1']['results'][(int)substr($value, 0, -2)]);
                }
            }
        }

        $_SESSION['spot-res'] = $temp_sp;
        $_SESSION['dc-res'] = $temp_dc;
    }

 }