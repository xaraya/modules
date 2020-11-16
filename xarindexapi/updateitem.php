<?php
function keywords_indexapi_updateitem(array $args=array())
{
    // there's absolutely no good reason to need this, once created an index never changes
    throw new ForbiddenOperationException(null, 'Changing an index is not permitted');
}
