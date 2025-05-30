import { Slot } from "radix-ui";
import * as React from "react";
import ArrowRight01Icon from "virtual:icons/hugeicons/arrow-right-01";

import { cn } from "#/utils/cn.ts";
import { type PolymorphicComponentProps } from "#/utils/polymorphic.ts";
import { recursiveCloneChildren } from "#/utils/recursive-clone-children.tsx";
import { tv, type VariantProps } from "#/utils/tv.ts";

const VERTICAL_STEPPER_ROOT_NAME = "VerticalStepperRoot";
const VERTICAL_STEPPER_ARROW_NAME = "VerticalStepperArrow";
const VERTICAL_STEPPER_ITEM_NAME = "VerticalStepperItem";
const VERTICAL_STEPPER_ITEM_INDICATOR_NAME = "VerticalStepperItemIndicator";

function VerticalStepperRoot({
    asChild,
    children,
    className,
    ...rest
}: React.HTMLAttributes<HTMLDivElement> & {
    asChild?: boolean;
}) {
    const Component = asChild ? Slot.Root : "div";
    return (
        <Component className={cn("w-full space-y-2", className)} {...rest}>
            {children}
        </Component>
    );
}
VerticalStepperRoot.displayName = VERTICAL_STEPPER_ROOT_NAME;

function VerticalStepperArrow<T extends React.ElementType>({ className, as, ...rest }: PolymorphicComponentProps<T>) {
    const Component = as || ArrowRight01Icon;

    return <Component className={cn("size-5 shrink-0 text-(--text-sub-600)", className)} {...rest} />;
}
VerticalStepperArrow.displayName = VERTICAL_STEPPER_ARROW_NAME;

const verticalStepperItemVariants = tv({
    slots: {
        root: [
            // base
            "grid w-full auto-cols-auto grid-flow-col grid-cols-[auto_minmax(0,1fr)] items-center gap-2.5 rounded-10 p-2 text-left text-paragraph-sm",
        ],
        indicator: [
            // base
            "flex size-5 shrink-0 items-center justify-center rounded-full text-label-xs",
        ],
    },
    variants: {
        $state: {
            completed: {
                root: "bg-(--bg-weak-50) text-(--text-sub-600)",
                indicator: "bg-state-success-base text-white",
            },
            active: {
                root: "bg-(--bg-white-0) text-(--text-strong-950) shadow-xs",
                indicator: "bg-primary text-white",
            },
            default: {
                root: "bg-(--bg-weak-50) text-(--text-sub-600)",
                indicator: "bg-(--bg-white-0) text-(--text-sub-600) shadow-xs",
            },
        },
    },
    defaultVariants: {
        $state: "default",
    },
});

type VerticalStepperItemSharedProps = VariantProps<typeof verticalStepperItemVariants>;

type VerticalStepperItemProps = React.ComponentPropsWithRef<"button"> &
    VariantProps<typeof verticalStepperItemVariants> & {
        asChild?: boolean;
    };

function VerticalStepperItem({ asChild, children, $state, className, ...rest }: VerticalStepperItemProps) {
    const uniqueId = React.useId();
    const Component = asChild ? Slot.Root : "button";
    const { root } = verticalStepperItemVariants({ $state });

    const sharedProps: VerticalStepperItemSharedProps = {
        $state,
    };

    const extendedChildren = recursiveCloneChildren(
        children as React.ReactElement[],
        sharedProps,
        [VERTICAL_STEPPER_ITEM_INDICATOR_NAME],
        uniqueId,
        asChild,
    );

    return (
        <Component className={root({ class: className })} {...rest}>
            {extendedChildren}
        </Component>
    );
}
VerticalStepperItem.displayName = VERTICAL_STEPPER_ITEM_NAME;

function VerticalStepperItemIndicator({
    $state,
    className,
    children,
    ...rest
}: React.HTMLAttributes<HTMLDivElement> & VerticalStepperItemSharedProps) {
    const { indicator } = verticalStepperItemVariants({ $state });

    if ($state === "completed") {
        return (
            <div className={indicator({ class: className })} {...rest}>
                <svg fill="none" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M15.1 7.453 8.726 13.82 4.9 10l1.275-1.274 2.55 2.548 5.1-5.094L15.1 7.453Z"
                        fill="currentColor"
                    />
                </svg>
            </div>
        );
    }

    return (
        <div className={indicator({ class: className })} {...rest}>
            {children}
        </div>
    );
}
VerticalStepperItemIndicator.displayName = VERTICAL_STEPPER_ITEM_INDICATOR_NAME;

export {
    VerticalStepperRoot as Root,
    VerticalStepperArrow as Arrow,
    VerticalStepperItem as Item,
    VerticalStepperItemIndicator as ItemIndicator,
};
