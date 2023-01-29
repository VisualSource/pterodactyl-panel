import deleteDomain from '@/api/admin/domains/deleteDomain';
import ConfirmationModal from '@/components/elements/ConfirmationModal';
import { Button } from '@/components/elements/button/index';
import { ApplicationStore } from '@/state';
import { Actions, useStoreActions } from 'easy-peasy';
import { useState } from 'react';
import { TrashIcon } from '@heroicons/react/outline';
import { Shape } from '@/components/elements/button/types';
import tw from 'twin.macro';

interface Props {
    domainId: number;
    onDeleted: () => void;
}

export default ({ domainId, onDeleted }: Props) => {
    const [visible, setVisible] = useState<boolean>(false);
    const [loading, setLoading] = useState<boolean>(false);

    const { clearFlashes, clearAndAddHttpError } = useStoreActions(
        (actions: Actions<ApplicationStore>) => actions.flashes,
    );

    const onDelete = async () => {
        try {
            setLoading(true);
            clearFlashes('domain');

            await deleteDomain(domainId);

            setLoading(false);
            onDeleted();
        } catch (error) {
            console.error(error);
            clearAndAddHttpError({ key: 'domain', error });

            setLoading(false);
            setVisible(false);
        }
    };

    return (
        <>
            <ConfirmationModal
                visible={visible}
                title="Delete Domain?"
                buttonText="Yes, delete domain"
                onConfirmed={onDelete}
                showSpinnerOverlay={loading}
                onModalDismissed={() => setVisible(false)}
            >
                Are you sure you want to delete this domain? This may disrupted some services.
            </ConfirmationModal>

            <Button.Danger type="button" shape={Shape.IconSquare} onClick={() => setVisible(true)}>
                <TrashIcon css={tw`w-1/2 h-1/2`} />
            </Button.Danger>
        </>
    );
};
