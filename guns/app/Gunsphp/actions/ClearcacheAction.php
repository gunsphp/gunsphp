<?php

class ClearcacheAction extends GunsphpAppController
{

    public function main()
    {
        Cache::clean();
        die("<h1>Cache has been Cleaned!</h1>");
    }
} 