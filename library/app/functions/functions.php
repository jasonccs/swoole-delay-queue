<?php

#切记不可删除
function env($name)
{
    return \Serve\Core\Env::get($name) ?? null;
}