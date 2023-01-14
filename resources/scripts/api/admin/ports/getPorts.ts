import http, { FractalResponseData, getPaginationSet, PaginatedResult } from '@/api/http';
import { useContext } from 'react';
import useSWR from 'swr';
import { createContext } from '@/api/admin';

export const portTypes = {
    UDP: "udp",
    TCP: "tcp",
    Both: "both"
} as const;

export const portMethods = {
    PMP: "pmp",
    UPNP: "upnp"
} as const;

export type PortType = typeof portTypes;
export type PortMethod = typeof portMethods;

export interface Port {
    id: number;
    allocation_id: number;
    internal_port: number | null;
    external_port: number;
    type: PortType
    method: PortMethod,
    description: string | null;
    internal_address: `${number}.${number}.${number}.${number}` | null;
    createdAt: Date;
    updatedAt: Date;
}

export const rawDataToPort = ({ attributes }: FractalResponseData): Port => {
    return {
        id: attributes.id,
        allocation_id: attributes.allocation_id,
        internal_port: attributes.internal_port,
        external_port: attributes.external_port,
        type: attributes.type,
        method: attributes.method,
        description: attributes.description,
        internal_address: attributes.internal_address,
        createdAt: new Date(attributes.created_at),
        updatedAt: new Date(attributes.updated_at)
    }
}

export interface Filters {
    id?: string;
    type?: string;
    method?: string;
    external_port?: string;
}

type FilterName = `filter[${string}]`;

export const Context = createContext<Filters>();

export default (include: string[] = []) => {
    const { page, filters, sort, sortDirection } = useContext(Context);
    const params: { [key: FilterName]: any; sort?: string; } = {};

    if(filters) {
        Object.keys(filters).forEach(key=>{
            params[`filter[${key}]`] = filters[key as keyof typeof filters];
        });
    }

    if(sort) {
        params.sort = `${sortDirection ? "-" : ""}${sort}`;
    }

    const includes = include.join(",");

    return useSWR<PaginatedResult<Port>>(["ports",page,filters,sort,sortDirection,includes],async () =>{
        const { data } = await http.get("/api/application/ports",{
            params: {
                include: includes,
                page,
                ...params
            }
        });

        return {
            items: (data.data ?? [].map(rawDataToPort)),
            pagination: getPaginationSet(data.meta.pagination),
        }
    });
}


