<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

interface ColorInterface
{
    public const NORMAL = "\033[0m";

    public const BLACK = "\033[0;30m";
    public const DARK_GRAY = "\033[1;30m";
    public const BLUE = "\033[0;34m";
    public const LIGHT_BLUE = "\033[1;34m";
    public const GREEN = "\033[0;32m";
    public const LIGHT_GREEN = "\033[1;32m";
    public const CYAN = "\033[0;36m";
    public const LIGHT_CYAN = "\033[1;36m";
    public const RED = "\033[0;31m";
    public const LIGHT_RED = "\033[1;31m";
    public const PURPLE = "\033[0;35m";
    public const LIGHT_PURPLE = "\033[1;35m";
    public const BROWN = "\033[0;33m";
    public const YELLOW = "\033[1;33m";
    public const LIGHT_GRAY = "\033[0;37m";
    public const WHITE = "\033[1;37m";

    public const BLACK_BACKGROUND = "\033[40m";
    public const RED_BACKGROUND = "\033[41m";
    public const GREEN_BACKGROUND = "\033[42m";
    public const YELLOW_BACKGROUND = "\033[43m";
    public const BLUE_BACKGROUND = "\033[44m";
    public const MAGENTA_BACKGROUND = "\033[45m";
    public const CYAN_BACKGROUND = "\033[46m";
    public const LIGHT_GRAY_BACKGROUND = "\033[47m";
}
