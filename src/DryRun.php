<?php

class DryRun {
	public static function clean() {
		\Patchwork\undoAll();
	}
}
