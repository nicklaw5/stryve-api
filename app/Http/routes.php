<?php

Route::group(['prefix' => 'api', 'middleware' => 'cors'], function() {

	// Authentication not required
	Route::post('auth/login', 'AuthController@login');												// log user in
	Route::post('auth/register', 'AuthController@register');										// register new user

	// Authentication required
	Route::group(['middleware' => ['api.auth.before',  'api.auth.after']], function() {
		
		// auth
		Route::post('auth/logout', 'AuthController@logout');										// log user out

		// users
		// Route::get('users', 'UsersController@index');												// returns all users known to the user through the servers they belong to
		Route::get('users/self', 'UsersController@self');											// return users own object

		// contacts
		Route::get('contacts', 'ContactsController@index');											// returns a users contact list
		Route::get('contacts/search', 'ContactsController@search');									// search for contacts
		Route::post('contacts/{uuid}', 'ContactsController@store');									// create a new user-contact relationship
		Route::delete('contacts/{uuid}', 'ContactsController@delete');								// delete a user-contact realtionship
		
		// contact events
		Route::get('contacts/{uuid}/events', 'ContactEventsController@index');						// return all events between two users
		Route::post('contacts/{uuid}/events', 'ContactEventsController@store');						// create a new event between two users
		Route::put('contacts/{uuid}/events/{event_uuid}', 'ContactEventsController@update');		// update an event bwteen two users
		Route::delete('contacts/{uuid}/events/{event_uuid}', 'ContactEventsController@delete');		// delete an event bwteen two users

		// regions
		Route::get('regions', 'RegionsController@index');											// return all server regions
		Route::post('regions', 'RegionsController@store');											// create a server region
		Route::put('regions/{uuid}', 'RegionsController@update');									// update a server region
		Route::delete('regions/{uuid}', 'RegionsController@delete');								// delete a server region

		// servers
		Route::get('servers/self', 'ServersController@self');										// return the servers the user belongs to
		Route::get('servers/{uuid}', 'ServersController@show');										// return a server
		Route::post('servers', 'ServersController@store');											// create a server
		Route::put('servers/{uuid}', 'ServersController@update');									// update a server
		Route::delete('servers/{uuid}', 'ServersController@delete');								// delete a server

		// server invitations
		Route::post('servers/{uuid}/invitations', 'ServerInvitationsController@store');				// returns a new server invitation token
		// Route::get('servers/{uuid}/invitations/{token}', 'ServerInvitationsController@show');	// returns the server the token represents

		// server events
		// Route::get('servers/{uuid}/events', 'ServerEventsController@show');						// return the recent events for this server
		Route::post('servers/{uuid}/events', 'ServerEventsController@store');						// create a server events
		// Route::put('servers/{uuid}/events/{id}', 'ServerEventsController@update');				// update an existing event

		// server channels
		Route::get('servers/{uuid}/channels', 'ServerChannelsController@index');					// return all server channels
		Route::get('servers/{uuid}/channels/{id}', 'ServerChannelsController@show');				// return a server channel
		Route::post('servers/{uuid}/channels', 'ServerChannelsController@store');					// create a server channel
		Route::put('servers/{uuid}/channels/{id}', 'ServerChannelsController@update');				// update a server channel
		Route::delete('servers/{uuid}/channels/{id}', 'ServerChannelsController@delete');			// delete a server channel

		// channel events
		Route::get('channels/{uuid}/events', 'ChannelEventsController@show');						// return the recent events for this channel
		Route::post('channels/{uuid}/events', 'ChannelEventsController@store');						// create a channel events
		// Route::put('channels/{uuid}/events/{id}', 'ChannelEventsController@update');				// update an existing event

		// invitions
		Route::get('invitations/{token}', 'ServerInvitationsController@show');						// accept an invitation to a channel
		// Route::post('servers/{uuid}/invites', 'ChatChannelInvitesController@store');				// create an invitation to a channel
	});

});