import Vue from 'vue';
import Vuex from 'vuex';
import { generateUrl } from '@nextcloud/router';

Vue.use(Vuex);

export default new Vuex.Store({
	state: {
		club: null,
		clubs: null,
		cities: null,
		members: null,
		nameFilter: null,
		selectedCities: new Set(),
	},
	getters: {
		club: state => state.club,
		clubs: state => state.clubs,
		cities: state => state.cities,
		selectedCities: state => {
			if (state.selectedCities.size === 0) {
				return state.cities;
			}

			return Array.from(state.selectedCities.values());
		},
		members: state => {
			if (state.members == null) {
				return null;
			}

			const selectedCities = state.selectedCities;
			const nameFilter = state.nameFilter;

			return state.members.filter(member => {
				if (nameFilter == null) return true;
				return member.fullnames.filter(name => name.toLowerCase().indexOf(nameFilter) !== -1).length > 0;
			}).filter(member => {
				return selectedCities.size === 0 || selectedCities.has(member.city);
			});
		},
	},
	mutations: {
		updateClubs(state, clubs) {
			state.clubs = clubs;

			if (clubs.length === 0) {
				state.cities = [];
				state.members = [];
			} else {
				state.cities = null;
				state.members = null;
			}
		},

		updateMembers(state, { members, cities, club }) {
			state.members = members;
			state.cities = cities;
			state.club = club;
		},

		updateNameFilter(state, nameFilter) {
			if (nameFilter != null) {
				state.nameFilter = nameFilter.toLowerCase();
			} else {
				state.nameFilter = null;
			}
		},

		updateSelectedCities(state, cities) {
			state.selectedCities = cities;
		},
	},
	actions: {
		fetchClubs({ commit }) {
			fetch(generateUrl('/apps/spgverein/clubs'))
				.then(response => response.json())
				.then(clubs => clubs.sort())
				.then(clubs => {
					commit('updateClubs', clubs);
				});
		},

		openClub({ commit, state }, club) {
			if (!state.clubs.indexOf(club) < 0) {
				return;
			}

			commit('updateMembers', {});

			fetch(generateUrl(`/apps/spgverein/cities/${club}`))
				.then(response => response.json())
				.then(cities => fetch(generateUrl(`/apps/spgverein/members/${club}`))
					.then(response => response.json())
					.then(members => {
						const regex = /(.*)\s+((\d+)\s*([a-z])?)/;

						return members.sort((m1, m2) => {
							let cmp = m1.city.localeCompare(m2.city);
							if (cmp === 0) {
								const str1 = m1.street.match(regex);
								const str2 = m2.street.match(regex);

								cmp = str1[1].localeCompare(str2[1]);
								if (cmp === 0) {
									const a = parseInt(str1[3]);
									const b = parseInt(str2[3]);

									if (a < b) { cmp = -1; } else if (a > b) { cmp = 1; } else { cmp = 0; }
								}
							}
							return cmp;
						});
					})
					.then(members => {
						commit('updateMembers', { members, cities, club });
					}));
		},

		filterMembersByCities({ commit }, cities) {
			commit('updateSelectedCities', new Set(cities));
		},

		filterByName({ commit }, nameFilter) {
			commit('updateNameFilter', nameFilter);
		},

		clearNameFilter({ commit }) {
			commit('updateNameFilter', null);
		},
	},
});
