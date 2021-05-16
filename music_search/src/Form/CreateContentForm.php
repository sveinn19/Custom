<?php
namespace Drupal\music_search\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Form for searching music.
 */

 class CreateContentForm extends FormBase{

    public function __construct($spotify_service, $discogs_service) {  
        $this->spotify_service = $spotify_service;
        $this->discogs_service = $discogs_service;
        }
    
        /** * {@inheritdoc} 
         * */
    public static function create(ContainerInterface $container) { 
         return new static(    
           $container->get('spotify_lookup.spotify_lookup'),
           $container->get('discogs_lookup.discogs_lookup')
           );
         }

    public function getFormId(){
        return 'create_content_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state){
        $result = $_SESSION['s1'][$_SESSION['s2'] . 's']['items'];//[$_SESSION['con']];
        $_SESSION['result'] = $result;

        $this->get_inputs();

        $form['nafn'] = array(
            '#type' => 'radios',
            '#title' => 'Choose name: ',
            '#options' => $this->getNames($_SESSION['spot-res'], $_SESSION['dc-res']),
            //'#suffix' => "<h2>Spotify</h2><pre>". print_r($_SESSION['spot-res'], true)."</pre>",
            //'#prefix' => "<h2>Discogs</h2><pre>". print_r($_SESSION['dc-res'], true)."</pre>",
            //'#suffix' => "<pre>".print_r($
        );

        $form['image'] = array(
            '#type' => 'radios',
            '#title' => 'Choose image: ',
            '#options' => $this->getImages($_SESSION['spot-res'], $_SESSION['dc-res']),
            //'#suffix' => "
        );

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

    private function getNames($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            if (!isset($option[$value['name']])){
                $option[$value['name']] = t($value['name']).t('  (Spotify)');
            }
        }

        foreach($discogs as $key => $value){
            if (!isset($option[$value['name']])){
                $option[$value['name']] = ($value['name']).t('  (Discogs)');
            }
            if (!isset($option[$value['realname']])){
                $option[$value['realname']] = ($value['realname']).t('  (Discogs)');
            }
        }


        return $option;
    }

    private function getImages($spotify, $discogs){
        $option = [];
        foreach($spotify as $key => $value){
            $option[$value['images'][0]['url']] = '<img src='. '"' . $value['images'][0]['url'] . '" width="100" > (  Spotify)';
            
        }

        foreach($discogs as $key => $value){
            $option[$value['cover_image']] = '<img src='. '"' . $value['cover_image'] . '" width="200"> (  Discogs)';
        }


        return $option;

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
                    $tmp = $this->discogs_service->_discogs_api_get_query($_SESSION['d1']['results'][(int)substr($value, 0, -2)]['resource_url']);
                    $tmp['cover_image'] = $_SESSION['d1']['results'][(int)substr($value, 0, -2)]['cover_image'];
                    array_push($temp_dc, $tmp);
                }
            }
        }

        $_SESSION['spot-res'] = $temp_sp;
        $_SESSION['dc-res'] = $temp_dc;
    }

 }