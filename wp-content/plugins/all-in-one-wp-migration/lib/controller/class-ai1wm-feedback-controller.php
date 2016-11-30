<?php
/**
 * Copyright (C) 2014-2016 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wm_Feedback_Controller {

	public static function feedback() {

		// Set Type
		$type = null;
		if ( isset( $_POST['ai1wm-type'] ) ) {
			$type = trim( $_POST['ai1wm-type'] );
		}

		// Set E-mail
		$email = null;
		if ( isset( $_POST['ai1wm-email'] ) ) {
			$email = trim( $_POST['ai1wm-email'] );
		}

		// Set Message
		$message = null;
		if ( isset( $_POST['ai1wm-message'] ) ) {
			$message = trim( $_POST['ai1wm-message'] );
		}

		// Set Terms
		$terms = false;
		if ( isset( $_POST['ai1wm-terms'] ) ) {
			$terms = (bool) $_POST['ai1wm-terms'];
		}

		// Send Feedback
		$model  = new Ai1wm_Feedback;
		$result = $model->add( $type, $email, $message, $terms );

		echo json_encode( $result );
		exit;
	}
}
