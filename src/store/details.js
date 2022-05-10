export const getters = {
	selectedMember: state => state.member,
};

export const mutations = {
	updateMember(state, { member }) {
		// eslint-disable-next-line
                  console.log('----> ', member);
		state.member = member;
	},
};

export const actions = {
	showMember({ commit }, member) {
		commit('updateMember', { member });
	},

	clearSelectedMember({ commit }) {
		commit('updateMember', { member: null });
	},
};

export default async function createDetailsStore() {
	return {
		state: { member: null },
		getters,
		mutations,
		actions,
	};
}
