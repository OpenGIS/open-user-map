import { registerBlockType } from '@wordpress/blocks';

import Edit from './edit';
import Save from './save';

registerBlockType( 
	'open-user-map/map',
	{
		edit: Edit,
		save: Save
	}
)