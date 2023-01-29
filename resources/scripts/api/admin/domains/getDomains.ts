import http, { type FractalResponseData, getPaginationSet, type PaginatedResult } from '@/api/http';
import { useContext } from 'react';
import useSWR from 'swr';
import { createContext } from '@/api/admin';

export interface ServerLike {
    name: string;
    identifier: string;
    id: number;
}

export interface Domain {
    id: number;
    domain: string;
    server_id: number | null;
    createdAt: Date;
    updatedAt: Date;
    server: null | ServerLike;
}

export const rawDataToDomain = ({ attributes }: FractalResponseData): Domain => {
    let server: ServerLike | null = null;

    if (attributes.server_id && attributes.relationships?.server) {
        const data = (attributes.relationships.server as FractalResponseData).attributes;
        server = {
            name: data.name,
            identifier: data.identifier,
            id: data.id,
        };
    }

    return {
        id: attributes.id,
        domain: attributes.domain,
        server_id: attributes.server_id,
        server,
        createdAt: new Date(attributes.created_at),
        updatedAt: new Date(attributes.updated_at),
    };
};

export interface Filters {
    id?: string;
    domain?: string;
}

type FilterName = `filter[${string}]`;

export const Context = createContext<Filters>();

export default (include: string[] = []) => {
    const { page, filters, sort, sortDirection } = useContext(Context);

    const params: { [key: FilterName]: any; sort?: string } = {};

    if (filters !== null) {
        Object.keys(filters).forEach(key => {
            params[`filter[${key}]`] = filters[key as keyof typeof filters];
        });
    }

    if (sort !== null) {
        params.sort = `${sortDirection ? '-' : ''}${sort}`;
    }

    const includes = include.join(',');

    return useSWR<PaginatedResult<Domain>>(['domains', page, filters, sort, sortDirection, includes], async () => {
        const { data } = await http.get('/api/application/domains', {
            params: {
                include: includes,
                page,
                ...params,
            },
        });

        return {
            items: (data.data ?? []).map(rawDataToDomain),
            pagination: getPaginationSet(data.meta.pagination),
        };
    });
};
