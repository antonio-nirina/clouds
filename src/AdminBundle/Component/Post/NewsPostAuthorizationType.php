<?php
namespace AdminBundle\Component\Post;

/**
 * Post authorization
 *
 * Post authorization, for eg for viewing a (news) post
 */
class NewsPostAuthorizationType
{
    const ALL = 'authorization-type-all';
    const BY_ROLE = 'authorization-type-by-role';
    const CUSTOM_LIST = 'authorization-type-custom-list';
}