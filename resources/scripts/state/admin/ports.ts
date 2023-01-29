import { type Action, action } from 'easy-peasy';

export interface AdminPortStore {
    selectedPorts: number[];
    setSelectedPorts: Action<AdminPortStore, number[]>;
    appendSelectedPort: Action<AdminPortStore, number>;
    removeSelectedPort: Action<AdminPortStore, number>;
}

const ports: AdminPortStore = {
    selectedPorts: [],

    setSelectedPorts: action((state, payload) => {
        state.selectedPorts = payload;
    }),

    appendSelectedPort: action((state, payload) => {
        state.selectedPorts = state.selectedPorts.filter(id => id !== payload).concat(payload);
    }),

    removeSelectedPort: action((state, payload) => {
        state.selectedPorts = state.selectedPorts.filter(id => id !== payload);
    }),
};

export default ports;
