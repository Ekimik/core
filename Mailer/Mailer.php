<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @since         3.1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Mailer;

use ArrayAccess;
use Cake\Datasource\ModelAwareTrait;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\Exception\MissingMailException;
use Cake\Utility\Inflector;

abstract class Mailer implements ArrayAccess, EventListenerInterface
{
    use ModelAwareTrait;

    /**
     * Layout.
     *
     * @var string
     */
    public $layout;

    /**
     * Email instance.
     *
     * @var \Cake\Mailer\Email
     */
    protected $_email;

    /**
     * Constructor.
     *
     * @param \Cake\Mailer\Email|null $email Email instance.
     */
    public function __construct(Email $email = null)
    {
        if ($email === null) {
            $email = new Email();
        }

        if ($this->layout === null) {
            $this->layout = Inflector::underscore($this->getName());
        }

        $this->_email = $email->profile((array)$this);
    }

    /**
     * Returns the mailer's name.
     *
     * @return string
     */
    public function getName()
    {
        return str_replace('Mailer', '', join('', array_slice(explode('\\', get_class($this)), -1)));
    }

    /**
     * Sets layout to use. Defaults to configured layout template if a custom layout
     * could not be found.
     *
     * @param string $layout Name of the layout to use.
     * @return $this object.
     */
    public function layout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Sets headers.
     *
     * @param array $headers Headers to set.
     * @return $this object.
     */
    public function setHeaders(array $headers)
    {
        $this->_email->setHeaders($headers);
        return $this;
    }

    /**
     * Adds headers.
     *
     * @param array $headers Headers to set.
     * @return $this object.
     */
    public function addHeaders(array $headers)
    {
        $this->_email->addHeaders($headers);
        return $this;
    }

    /**
     * Sets attachments.
     *
     * @param string|array $attachments String with the filename or array with filenames
     * @return $this object.
     * @throws \InvalidArgumentException
     */
    public function attachments($attachments)
    {
        $this->_email->attachments($attachments);
        return $this;
    }

    /**
     * Sets email view vars.
     *
     * @param string|array $key Variable name or hash of view variables.
     * @param mixed $value View variable value.
     * @return $this object.
     */
    public function set($key, $value = null)
    {
        $this->_email->viewVars(is_string($key) ? [$key => $value] : $key);
        return $this;
    }

    /**
     * Sends email.
     *
     * @param string $mail The name of the mail action to trigger.
     * @param array $args Arguments to pass to the triggered mail action.
     * @param array $headers Headers to set.
     * @return array
     * @throws \Cake\Mailer\Exception\MissingMailException
     * @throws \BadMethodCallException
     */
    public function send($mail, $args = [], $headers = [])
    {
        if (!method_exists($this, $mail)) {
            throw new MissingMailException([
                'mailer' => $this->getName() . 'Mailer',
                'mail' => $mail,
            ]);
        }

        $this->setHeaders($headers);

        call_user_func_array([$this, $mail], $args);

        $result = $this->_email
            ->profile((array)$this)
            ->send();

        $this->reset();
        return $result;
    }

    /**
     * Resets email instance to original config.
     *
     * @return $this object.
     */
    public function reset()
    {
        $this->_email->reset();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return in_array($offset, get_object_vars($this)) ||
            method_exists($this->_email, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw \Exception();
        }

        if (isset($this->{$offset})) {
            return $this->{$offset};
        }

        return call_user_func([$this->_email, $offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * Implemented events.
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [];
    }
}
