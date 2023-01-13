import type { ChangeEvent } from 'react';
import { useContext, useEffect } from 'react';
import { NavLink } from 'react-router-dom';
import tw from 'twin.macro';

import getDomains, { Context as DomainsContext, type Filters } from '@/api/admin/domains/getDomains';
import AdminContentBlock from '@/components/admin/AdminContentBlock';
import AdminCheckbox from '@/components/admin/AdminCheckbox';
import AdminTable, {
    TableBody,
    TableHead,
    TableHeader,
    TableRow,
    Pagination,
    Loading,
    NoItems,
    ContentWrapper,
    useTableHooks,
} from '@/components/admin/AdminTable';
import NewDomainButton from './NewDomainButton';
import CopyOnClick from '@/components/elements/CopyOnClick';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';
import { AdminContext } from '@/state/admin';
import DomainEditButton from './DomainEditButton';

function RowCheckbox({ id }: { id: number }) {
    const isChecked = AdminContext.useStoreState(state => state.locations.selectedLocations.indexOf(id) >= 0);
    const appendSelectedDomain = AdminContext.useStoreActions(actions => actions.domains.appendSelectedDomains);
    const removeSelectedDomain = AdminContext.useStoreActions(actions => actions.domains.removeSelectedDomains);

    return (
        <AdminCheckbox
            name={id.toString()}
            checked={isChecked}
            onChange={(e: ChangeEvent<HTMLInputElement>) => {
                if (e.currentTarget.checked) 
                    return appendSelectedDomain(id);

                removeSelectedDomain(id);
            }}
        />
    );
}

function DomainsContainer() {
    const { page, setPage, setFilters, sort, setSort, sortDirection } = useContext(DomainsContext);
    const { clearFlashes, clearAndAddHttpError } = useFlash();
    const { data: domains, error, isValidating } = getDomains(["server"]);

    useEffect(()=>{
        if(!error) {
            clearFlashes("domains");
            return;
        }

        clearAndAddHttpError({ key: "domains", error });
    },[error]);

    const length = domains?.items.length ?? 0;

    const setSelectedDomains = AdminContext.useStoreActions(actions=>actions.domains.setSelectedDomains);
    const selectedDomainsLength = AdminContext.useStoreState(state => state.domains.selectedDomains.length);

    const onSelectAllClick = (e: ChangeEvent<HTMLInputElement>) => {
        setSelectedDomains(e.currentTarget.checked ? domains?.items.map(domain=>domain.id) ?? [] : []);
    }

    const onSearch = (query: string): Promise<void> => {
        return new Promise(ok => {
            if(query.length < 2) return ok(setFilters(null));
            return ok(setFilters({ domain: query }));
        });
    }

    useEffect(() => {
        setSelectedDomains([]);
    }, [page]);

    return (
        <AdminContentBlock title="Domains">
            <div css={tw`w-full flex flex-row items-center mb-8`}>
                <div css={tw`flex flex-col flex-shrink`} style={{ minWidth: '0' }}>
                    <h2 css={tw`text-2xl text-neutral-50 font-header font-medium`}>Domains</h2>
                    <p css={tw`text-base text-neutral-400 whitespace-nowrap overflow-ellipsis overflow-hidden`}>
                        All domains that nodes can be assigned to for easier categorization.
                    </p>
                </div>

                <div css={tw`flex ml-auto pl-4`}>
                    <NewDomainButton />
                </div>
            </div>

            <FlashMessageRender byKey={'domains'} css={tw`mb-4`} />

            <AdminTable>
                <ContentWrapper
                    checked={selectedDomainsLength === (length === 0 ? -1 : length)}
                    onSelectAllClick={onSelectAllClick}
                    onSearch={onSearch}
                >
                    <Pagination data={domains} onPageSelect={setPage}>
                        <div css={tw`overflow-x-auto`}>
                            <table css={tw`w-full table-auto`}>
                                <TableHead>
                                    <TableHeader
                                        name={'ID'}
                                        direction={sort === 'id' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('id')}
                                    />
                                    <TableHeader
                                        name={'Domain'}
                                        direction={sort === 'domain' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('domain')}
                                    />
                                    <TableHeader
                                        name={'Server'}
                                        direction={sort === 'server_id' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('server_id')}
                                    />
                                    <TableHeader
                                        name={'Updated'}
                                        direction={sort === 'updated_at' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('updated_at')}
                                    />
                                    <TableHeader
                                        name={'Created'}
                                        direction={sort === 'created_at' ? (sortDirection ? 1 : 2) : null}
                                        onClick={() => setSort('created_at')}
                                    />
                                </TableHead>

                                <TableBody>
                                    {domains !== undefined &&
                                        !error &&
                                        !isValidating &&
                                        length > 0 &&
                                            domains.items.map(domain => (
                                            <TableRow key={domain.id}>
                                                <td css={tw`pl-6`}>
                                                    <RowCheckbox id={domain.id} />
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    <CopyOnClick text={domain.id.toString()}>
                                                        <code css={tw`font-mono bg-neutral-900 rounded py-1 px-2`}>
                                                            {domain.id}
                                                        </code>
                                                    </CopyOnClick>
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    <DomainEditButton data={domain} />
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    { domain.server_id ? (
                                                        <NavLink
                                                         to={`/admin/servers/${domain.server_id}`}
                                                         css={tw`text-primary-400 hover:text-primary-300`}
                                                        >
                                                            { domain.server_id ? `${domain.server?.name ?? "Unkown Server"} | ${domain.server?.identifier ?? ""}` : "Unkown Server"}
                                                        </NavLink>
                                                    ) : (
                                                        <span css={tw`text-neutral-200`}>No Server set.</span>
                                                    ) }
                                                </td>

                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    {domain.updatedAt.toDateString()} - {domain.updatedAt.toLocaleTimeString()}
                                                </td>
                                                <td css={tw`px-6 text-sm text-neutral-200 text-left whitespace-nowrap`}>
                                                    {domain.createdAt.toDateString()} - {domain.createdAt.toLocaleTimeString()}
                                                </td>
                                            </TableRow>
                                        ))}
                                </TableBody>
                            </table>

                            {domains === undefined || (error && isValidating) ? (
                                <Loading />
                            ) : length < 1 ? (
                                <NoItems />
                            ) : null}
                        </div>
                    </Pagination>

                </ContentWrapper>
            </AdminTable>

        </AdminContentBlock>
    );
}

export default () => {
    const hooks = useTableHooks<Filters>();

    return (
        <DomainsContext.Provider value={hooks}>
            <DomainsContainer/>
        </DomainsContext.Provider>
    );
}