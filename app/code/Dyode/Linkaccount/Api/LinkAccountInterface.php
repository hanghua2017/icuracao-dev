<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       17/09/2018
 */

namespace Dyode\Linkaccount\Api;

/**
 * Link Account management
 * @api
 * @since 100.0.2
 */
interface LinkAccountInterface
{
    /**
     * Create account
     *
     * @param string $curacaoid
     * @param string $email
     * @param string $fname
     * @param string $lname
     * @param string $pass
     * @return bool
     */
    public function create($curacaoid,$email,$fname,$lname,$pass);

}
