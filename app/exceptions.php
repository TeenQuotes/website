<?php

class HiddenProfileException extends Exception {};
class TQNotFoundException extends Exception {};

// Sub classes of TQNotFoundException
class QuoteNotFoundException extends TQNotFoundException {};
class StoryNotFoundException extends TQNotFoundException {};
class UserNotFoundException extends TQNotFoundException {};
class TokenNotFoundException extends TQNotFoundException {};