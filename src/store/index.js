import Vue from 'vue';
import Vuex from 'vuex';
import { generateUrl } from '@nextcloud/router';
import dayjs from 'dayjs';
import createLabelsStore from './labels.js';

Vue.use(Vuex);

function compareWith(a, b, comparator) {
	if (a == null && b == null) {
		return 0;
	}
	if (a == null) {
		return 1;
	}
	if (b == null) {
		return -1;
	}
	return comparator(a, b);
}

export const getters = {
	club: state => state.club,
	clubs: state => state.clubs,
	cities: state => {
		const cities = state.members.map(m => m.city)
			.filter(c => c != null)
			.filter((v, i, a) => a.indexOf(v) === i);

		cities.sort();

		return cities;
	},
	error: state => {
		if (state.error == null) {
			return null;
		}
		return state.error.detail;
	},
	selectedCities: (state, getters) => {
		if (state.selectedCities.size === 0) {
			return getters.cities;
		}

		return Array.from(state.selectedCities.values());
	},
	members: state => {
		if (state.members == null) {
			return null;
		}

		const selectedCities = state.selectedCities;
		const nameFilter = state.nameFilter;

		const regex = /(.*)\s+((\d+)\s*([a-z])?)/;
		function sort(m1, m2) {
			let cmp = compareWith(m1.city, m2.city, (c1, c2) => c1.localeCompare(c2));
			if (cmp === 0) {
				cmp = compareWith(m1.street, m2.street, (s1, s2) => {
					const str1 = s1.match(regex);
					const str2 = s2.match(regex);

					if (str1 == null) {
						return 1;
					}
					if (str2 == null) {
						return -1;
					}

					let c = str1[1].localeCompare(str2[1]);
					if (c === 0) {
						const a = parseInt(str1[3]);
						const b = parseInt(str2[3]);

						if (a < b) { c = -1; } else if (a > b) { c = 1; } else { c = 0; }
					}
					return c;
				});
			}
			return cmp;
		}

		return state.members.filter(member => {
			if (nameFilter == null) return true;
			return member.fullnames.filter(name => name.toLowerCase().indexOf(nameFilter) !== -1).length > 0;
		}).filter(member => {
			return selectedCities == null || selectedCities.size === 0 || selectedCities.has(member.city);
		}).sort(sort);
	},
};

export const mutations = {
	updateClubs(state, clubs) {
		state.clubs = clubs;

		if (clubs.length === 0) {
			state.members = [];
		} else {
			state.members = null;
		}
	},

	updateMembers(state, { members, club, error }) {
		if (members != null) {
			state.members = members.map(member => {
				let birth = null;
				if (member.birth != null) {
					birth = dayjs(member.birth);
				}

				let admissionDate = null;
				if (member.admissionDate != null) {
					admissionDate = dayjs(member.admissionDate);
				}

				let resignationDate = null;
				if (member.resignationDate != null) {
					resignationDate = dayjs(member.resignationDate);
				}
				return { ...member, birth, admissionDate, resignationDate };
			});
		} else {
			state.members = null;
		}
		state.club = club;
		state.error = error;
		state.selectedCities.clear();
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
};

export const actions = {
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

		fetch(generateUrl(`/apps/spgverein/members/${club}`))
			.then(response => {
				if (response.headers.get('content-type') === 'application/problem+json') {
					return response.json().then(error => ({ members: [], error }));
				}

				return response.json().then(members => ({ members, error: null }));
			})
			.then(({ members, error }) => {
				commit('updateMembers', { members, club, error });
			});
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
};

export default async function createStore() {
	const labelsStore = await createLabelsStore();

	return new Vuex.Store({
		state: {
			club: null,
			clubs: null,
			members: null,
			nameFilter: null,
			selectedCities: new Set(),
			error: null,
		},
		getters,
		mutations,
		actions,
		modules: {
			labels: labelsStore,
		},
	});
}
