<?php
namespace AdminBundle\Component\Authorization;

/**
 * Authorization type (for eg ,for viewing specific data)
 */
class AuthorizationType
{
    const ALL = 'authorization-type-all';
    const BY_ROLE = 'authorization-type-by-role';
    const CUSTOM_LIST = 'authorization-type-custom-list';
}