<?php
/**
  * Solution for assignment 2
  * @author Daniel Toll
  */
/**
 * The settings file contains installation specific information
 * 
 */
class Settings {


	/**
	 * The app session name allows different apps on the same webhotel to share a virtual session
	 */
	const APP_SESSION_NAME = "Assignment4";
	
	/**
	 * Username of default user
	 */
	const USERNAME = "Admin";

	/**
	 * Password of default user
	 */
	const PASSWORD = "Password";

	/**
	 * Path to folder writable by www-data but not accessable by webserver
	 */
	const DATAPATH = "./model/DataForTempCredentials/";

	/**
	 * Salt for creating temporary passwords
	 * Should be a random string like "feje3-#GS"
	 */
	const SALT = "bnjytgvr567yuhgfdvgbhju876yukjhbgfghuji876tyujhgty876tyujhgfty785redse34er";

	/**
	 * Show errors 
	 * boolean true | false
	 */
	const DISPLAY_ERRORS = true;
}