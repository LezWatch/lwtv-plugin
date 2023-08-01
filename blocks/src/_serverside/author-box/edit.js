/**
 * External dependencies
 */

import { Component, Fragment } from '@wordpress/element';
import { SelectControl, PanelBody, TextControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';

import ServerSideRender from '@wordpress/server-side-render';

class AuthorProfileBlock extends Component {
	render() {
		const { attributes, setAttributes } = this.props;
		const { users, format } = attributes;
		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={'Team Member Settings'}>
					<TextControl
						label={'Username'}
						help={
							'Username or ID of team member (i.e. liljimmi, ipstenu, saralance)'
						}
						value={users}
						onChange={(value) => setAttributes({ users: value })}
					/>
					<SelectControl
						label={'Card Format'}
						type="string"
						value={format}
						options={[
							{ label: 'Large', value: 'large' },
							{ label: 'Compact', value: 'compact' },
							{ label: 'Thumbnail', value: 'thumbnail' },
						]}
						onChange={(value) => setAttributes({ format: value })}
					/>
				</PanelBody>
			</InspectorControls>
		);

		return (
			<Fragment>
				{inspectorControls}
				<ServerSideRender
					block="lwtv/author-box"
					attributes={attributes}
				/>
			</Fragment>
		);
	}
}

export default AuthorProfileBlock;
